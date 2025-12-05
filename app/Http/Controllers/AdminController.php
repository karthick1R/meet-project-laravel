<?php

namespace App\Http\Controllers;

use App\Mail\NewUserCredentials;
use App\Models\Booking;
use App\Models\Room;
use App\Models\RoomAvailability;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    /**
     * Display room management page
     */
    public function rooms()
    {
        $rooms = Room::withCount('bookings')->get();
        return view('admin.rooms.index', compact('rooms'));
    }

    /**
     * Store a new room
     */
    public function storeRoom(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'capacity' => 'required|integer|min:1',
            'location' => 'nullable|string|max:255',
            'amenities' => 'nullable|string',
            'color' => 'nullable|string|max:7',
            'is_active' => 'nullable|in:true,false,1,0,on,off',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $amenities = $request->amenities ? explode(',', $request->amenities) : [];

        $room = Room::create([
            'name' => $request->name,
            'description' => $request->description,
            'capacity' => $request->capacity,
            'location' => $request->location,
            'amenities' => $amenities,
            'color' => $request->color ?? '#3b82f6',
            'is_active' => $request->boolean('is_active'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Room created successfully!',
            'room' => $room
        ]);
    }

    public function updateRoom(Request $request, $id)
    {
        $room = Room::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'capacity' => 'required|integer|min:1',
            'location' => 'nullable|string|max:255',
            'amenities' => 'nullable|string',
            'color' => 'nullable|string|max:7',
            'is_active' => 'nullable|in:true,false,1,0,on,off',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $amenities = $request->amenities ? explode(',', $request->amenities) : [];

        $room->update([
            'name' => $request->name,
            'description' => $request->description,
            'capacity' => $request->capacity,
            'location' => $request->location,
            'amenities' => $amenities,
            'color' => $request->color ?? $room->color,
            'is_active' => $request->boolean('is_active'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Room updated successfully!',
            'room' => $room
        ]);
    }


    /**
     * Delete a room
     */
    public function deleteRoom($id)
    {
        $room = Room::findOrFail($id);

        // Check if room has active bookings
        $hasActiveBookings = Booking::where('room_id', $id)
            ->where('status', '!=', 'cancelled')
            ->where('date', '>=', today())
            ->exists();

        if ($hasActiveBookings) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete room with active bookings.'
            ], 422);
        }

        $room->delete();

        return response()->json([
            'success' => true,
            'message' => 'Room deleted successfully!'
        ]);
    }

    /**
     * Display all bookings with filters
     */
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

        return view('admin.bookings.index', compact('bookings', 'rooms'));
    }

    // Product key management removed - payment functionality removed

    /**
     * Block a time slot for maintenance
     */
    public function blockTimeSlot(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'room_id' => 'required|exists:rooms,id',
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'type' => 'required|in:blocked,maintenance',
            'reason' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Check for conflicting bookings
        $conflicting = Booking::where('room_id', $request->room_id)
            ->where('date', $request->date)
            ->where('status', '!=', 'cancelled')
            ->where(function ($query) use ($request) {
                $query->where(function ($q) use ($request) {
                    $q->where('start_time', '<', $request->end_time)
                        ->where('end_time', '>', $request->start_time);
                });
            })
            ->exists();

        if ($conflicting) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot block time slot with existing bookings.'
            ], 422);
        }

        $availability = RoomAvailability::create([
            'room_id' => $request->room_id,
            'date' => $request->date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'type' => $request->type,
            'reason' => $request->reason,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Time slot blocked successfully!',
            'availability' => $availability
        ]);
    }

    /**
     * Display user management page
     */
    public function users()
    {
        $users = User::orderByDesc('created_at')->paginate(25);
        return view('admin.users.index', compact('users'));
    }

    /**
     * Get user details for editing
     */
    public function getUser($id)
    {
        $user = User::findOrFail($id);
        return response()->json($user);
    }

    /**
     * Store a new user
     */
    public function storeUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'nullable|string|max:25',
            'password' => 'required|string|min:8',
            'role' => 'required|in:admin,user,super_admin',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $plainPassword = $request->password;

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($plainPassword),
            'role' => $request->role,
        ]);

        // Handle logo upload
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('logos', 'public');
            $user->update(['logo' => $logoPath]);
        }

        if (auth()->user()?->isSuperAdmin()) {
            Mail::to($user->email)->send(new NewUserCredentials(
                $user,
                $plainPassword,
                route('login')
            ));
        }

        return response()->json([
            'success' => true,
            'message' => 'User created successfully!',
            'user' => $user
        ]);
    }

    /**
     * Update a user
     */
    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
            'phone' => 'nullable|string|max:25',
            'password' => 'nullable|string|min:8',
            'role' => 'required|in:admin,user,super_admin',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'role' => $request->role,
        ];

        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        // Handle logo upload
        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($user->logo) {
                Storage::disk('public')->delete($user->logo);
            }
            $logoPath = $request->file('logo')->store('logos', 'public');
            $updateData['logo'] = $logoPath;
        }

        $user->update($updateData);

        return response()->json([
            'success' => true,
            'message' => 'User updated successfully!',
            'user' => $user
        ]);
    }

    /**
     * Delete a user
     */
    public function deleteUser($id)
    {
        $user = User::findOrFail($id);

        // Prevent deleting yourself
        if ($user->id === auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot delete your own account.'
            ], 422);
        }

        // Delete logo if exists
        if ($user->logo) {
            Storage::disk('public')->delete($user->logo);
        }

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully!'
        ]);
    }
}
