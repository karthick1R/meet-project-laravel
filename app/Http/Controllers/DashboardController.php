<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Room;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Show the application dashboard
     */
    public function index()
    {
        $user = auth()->user();

        if ($user->isSuperAdmin()) {
            return $this->superAdminDashboard();
        }

        if ($user->isAdmin()) {
            return $this->adminDashboard();
        }

        return $this->userDashboard();
    }

    /**
     * Admin dashboard
     */
    private function adminDashboard()
    {
        $totalRooms = Room::count();
        $activeRooms = Room::where('is_active', true)->count();
        $todayBookings = Booking::where('date', today())
            ->where('status', '!=', 'cancelled')
            ->count();
        $upcomingBookings = Booking::where('date', '>=', today())
            ->where('status', '!=', 'cancelled')
            ->count();

        $recentBookings = Booking::with(['room', 'user'])
            ->where('status', '!=', 'cancelled')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('dashboard.admin', compact(
            'totalRooms',
            'activeRooms',
            'todayBookings',
            'upcomingBookings',
            'recentBookings'
        ));
    }

    /**
     * Super Admin dashboard
     */
    private function superAdminDashboard()
    {
        $totalRooms = Room::count();
        $activeRooms = Room::where('is_active', true)->count();
        $totalUsers = User::count();
        $totalAdmins = User::whereIn('role', ['admin', 'super_admin'])->count();
        $todayBookings = Booking::where('date', today())
            ->where('status', '!=', 'cancelled')
            ->count();
        $upcomingBookings = Booking::where('date', '>=', today())
            ->where('status', '!=', 'cancelled')
            ->count();

        $recentBookings = Booking::with(['room', 'user'])
            ->where('status', '!=', 'cancelled')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $recentUsers = User::orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('dashboard.super-admin', compact(
            'totalRooms',
            'activeRooms',
            'totalUsers',
            'totalAdmins',
            'todayBookings',
            'upcomingBookings',
            'recentBookings',
            'recentUsers'
        ));
    }

    /**
     * User dashboard
     */
    private function userDashboard()
    {
        $rooms = Room::where('is_active', true)->get();
        $upcomingBookings = Booking::where('user_id', auth()->id())
            ->where('date', '>=', today())
            ->where('status', '!=', 'cancelled')
            ->with('room')
            ->orderBy('date')
            ->orderBy('start_time')
            ->limit(5)
            ->get();

        return view('dashboard.user', compact('rooms', 'upcomingBookings'));
    }
}
