<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RoomController extends Controller
{
    /**
     * Display a listing of available rooms
     */
    public function index()
    {
        $rooms = Room::where('is_active', true)
            ->withCount('bookings')
            ->get();

        return view('rooms.index', compact('rooms'));
    }

    /**
     * Show room details with calendar
     */
    public function show($id)
    {
        $room = Room::with(['bookings' => function ($query) {
            $query->where('status', '!=', 'cancelled')
                  ->where('date', '>=', now()->toDateString())
                  ->orderBy('date')
                  ->orderBy('start_time');
        }])->findOrFail($id);

        return view('rooms.show', compact('room'));
    }

    /**
     * Get room availability for a specific date (AJAX)
     */
    public function availability($id, Request $request)
    {
        $room = Room::findOrFail($id);

        $date = $request->input('date', now()->toDateString());
        $bookings = Booking::where('room_id', $id)
            ->where('date', $date)
            ->where('status', '!=', 'cancelled')
            ->get();

        $bookedSlots = $bookings->map(function ($booking) {
            return [
                'start' => $booking->start_time,
                'end' => $booking->end_time,
                'title' => $booking->title,
            ];
        });

        return response()->json([
            'room' => $room,
            'booked_slots' => $bookedSlots,
        ]);
    }
}
