<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Room;
use App\Models\Invitation;
use App\Mail\BookingCreated;
use App\Mail\BookingCancelled;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class BookingController extends Controller
{
    /**
     * Display a listing of bookings for the authenticated user
     */
    public function index()
    {
        $bookings = Booking::where('user_id', auth()->id())
            ->with(['room', 'invitations'])
            ->where('date', '>=', now()->toDateString())
            ->where('status', '!=', 'cancelled')
            ->orderBy('date')
            ->orderBy('start_time')
            ->get();

        return view('bookings.index', compact('bookings'));
    }

    /**
     * Get bookings for calendar (AJAX endpoint)
     */
    public function calendar(Request $request)
    {
        $start = $request->input('start', now()->startOfMonth()->toDateString());
        $end = $request->input('end', now()->endOfMonth()->toDateString());
        $roomId = $request->input('room_id');

        $query = Booking::with(['room', 'user'])
            ->where('status', '!=', 'cancelled')
            ->whereBetween('date', [$start, $end]);

        if ($roomId) {
            $query->where('room_id', $roomId);
        }

        // If user is not admin, only show their bookings
        if (!auth()->user()->isAdmin()) {
            $query->where('user_id', auth()->id());
        }

        $bookings = $query->get();

        $events = $bookings->map(function ($booking) {
            $startDateTime = Carbon::parse($booking->date)->setTimeFromTimeString($booking->start_time);
            $endDateTime = Carbon::parse($booking->date)->setTimeFromTimeString($booking->end_time);

            return [
                'id' => $booking->id,
                'title' => $booking->title . ' - ' . $booking->room->name,
                'start' => $startDateTime->toIso8601String(),
                'end' => $endDateTime->toIso8601String(),
                'color' => $booking->room->color,
                'room_id' => $booking->room_id,
                'room_name' => $booking->room->name,
                'description' => $booking->description,
                'status' => $booking->status,
            ];
        });

        return response()->json($events);
    }

    /**
     * Check availability for a time slot (AJAX endpoint)
     */
    public function checkAvailability(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'room_id' => 'required|exists:rooms,id',
            'date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'exclude_booking_id' => 'nullable|exists:bookings,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'available' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        $room = Room::findOrFail($request->room_id);
        $available = $room->isAvailable(
            $request->date,
            $request->start_time,
            $request->end_time,
            $request->exclude_booking_id
        );

        return response()->json([
            'available' => $available,
            'message' => $available ? 'Time slot is available' : 'Time slot is not available'
        ]);
    }

    /**
     * Store a newly created booking
     * Uses database transaction to prevent double-booking
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'room_id' => 'required|exists:rooms,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'recurrence' => 'nullable|in:none,daily,weekly,monthly',
            'recurrence_end_date' => 'nullable|date|after:date',
            'attendees' => 'nullable|string', // Comma-separated emails
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $room = Room::findOrFail($request->room_id);

        // Check if room is active
        if (!$room->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'This room is not available for booking.'
            ], 422);
        }

        // Use database transaction to prevent race conditions
        return DB::transaction(function () use ($request, $room) {
            // Double-check availability within transaction (with lock)
            $available = $room->isAvailable(
                $request->date,
                $request->start_time,
                $request->end_time
            );

            if (!$available) {
                return response()->json([
                    'success' => false,
                    'message' => 'This time slot is already booked or blocked. Please choose another time.'
                ], 422);
            }

            // Create booking
            $booking = Booking::create([
                'room_id' => $request->room_id,
                'user_id' => auth()->id(),
                'title' => $request->title,
                'description' => $request->description,
                'date' => $request->date,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'recurrence' => $request->recurrence ?? 'none',
                'recurrence_end_date' => $request->recurrence_end_date,
                'status' => 'confirmed',
            ]);

            // Handle recurring bookings
            if ($request->recurrence && $request->recurrence !== 'none' && $request->recurrence_end_date) {
                $this->createRecurringBookings($booking, $request->recurrence, $request->recurrence_end_date);
            }

            // Create invitations for attendees
            if ($request->attendees) {
                $emails = array_filter(array_map('trim', explode(',', $request->attendees)));
                foreach ($emails as $email) {
                    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        Invitation::create([
                            'booking_id' => $booking->id,
                            'email' => $email,
                            'status' => 'pending',
                        ]);

                        // Send email notification
                        try {
                            Mail::to($email)->send(new BookingCreated($booking));
                        } catch (\Exception $e) {
                            // Log error but don't fail booking
                            \Log::error('Failed to send booking email: ' . $e->getMessage());
                        }
                    }
                }
            }

            // Send email to organizer
            try {
                Mail::to(auth()->user()->email)->send(new BookingCreated($booking));
            } catch (\Exception $e) {
                \Log::error('Failed to send booking email to organizer: ' . $e->getMessage());
            }

            return response()->json([
                'success' => true,
                'message' => 'Booking created successfully!',
                'booking' => $booking->load(['room', 'invitations'])
            ]);
        });
    }

    /**
     * Create recurring bookings
     */
    private function createRecurringBookings(Booking $originalBooking, string $recurrence, string $endDate)
    {
        $currentDate = Carbon::parse($originalBooking->date);
        $endDate = Carbon::parse($endDate);
        $bookings = [];

        while ($currentDate->lte($endDate)) {
            if ($currentDate->eq($originalBooking->date)) {
                $currentDate = $this->getNextRecurrenceDate($currentDate, $recurrence);
                continue;
            }

            // Check availability before creating
            $room = $originalBooking->room;
            if ($room->isAvailable($currentDate->toDateString(), $originalBooking->start_time, $originalBooking->end_time)) {
                $booking = Booking::create([
                    'room_id' => $originalBooking->room_id,
                    'user_id' => $originalBooking->user_id,
                    'title' => $originalBooking->title,
                    'description' => $originalBooking->description,
                    'date' => $currentDate->toDateString(),
                    'start_time' => $originalBooking->start_time,
                    'end_time' => $originalBooking->end_time,
                    'recurrence' => $originalBooking->recurrence,
                    'recurrence_end_date' => $originalBooking->recurrence_end_date,
                    'status' => 'confirmed',
                ]);
                $bookings[] = $booking;
            }

            $currentDate = $this->getNextRecurrenceDate($currentDate, $recurrence);
        }

        return $bookings;
    }

    /**
     * Get next recurrence date
     */
    private function getNextRecurrenceDate(Carbon $date, string $recurrence): Carbon
    {
        return match ($recurrence) {
            'daily' => $date->copy()->addDay(),
            'weekly' => $date->copy()->addWeek(),
            'monthly' => $date->copy()->addMonth(),
            default => $date,
        };
    }

    /**
     * Cancel a booking
     */
    public function cancel($id)
    {
        // ✅ Correct query grouping
        $booking = Booking::where(function ($query) {
            $query->where('user_id', auth()->id())
                ->orWhereHas('invitations', function ($q) {
                    $q->where('email', auth()->user()->email);
                });
        })
            ->where('id', $id)
            ->firstOrFail();

        if ($booking->status === 'cancelled') {
            return response()->json([
                'success' => false,
                'message' => 'Booking is already cancelled.'
            ], 422);
        }

        // ✅ Update booking status
        $booking->status = 'cancelled';
        $booking->save();

        // ✅ Send cancellation emails
        $emails = $booking->invitations->pluck('email')->toArray();
        $emails[] = $booking->user->email;
        $emails = array_unique($emails);

        foreach ($emails as $email) {
            try {
                Mail::to($email)->send(new BookingCancelled($booking));
            } catch (\Exception $e) {
                \Log::error('Failed to send cancellation email: ' . $e->getMessage());
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Booking cancelled successfully.'
        ]);
    }


    /**
     * Show booking details
     */
    public function show($id)
    {
        $booking = Booking::with(['room', 'user', 'invitations'])
            ->findOrFail($id);

        // Check if user has permission to view
        if (!$booking->user_id === auth()->id() && !auth()->user()->isAdmin()) {
            abort(403);
        }

        return view('bookings.show', compact('booking'));
    }

    public function bookings(Request $request)
    {
        $query = Booking::with(['room', 'user']);

        // Filters
        if ($request->room_id) {
            $query->where('room_id', $request->room_id);
        }

        if ($request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->date) {
            $query->where('date', $request->date);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $bookings = $query->orderBy('date', 'desc')
            ->orderBy('start_time')
            ->paginate(20);

        $rooms = Room::where('is_active', true)->get();

        return view('bookings.overall', compact('bookings', 'rooms'));
    }
}
