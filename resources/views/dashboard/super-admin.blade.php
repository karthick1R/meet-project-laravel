@extends('layouts.app')

@section('title', 'Super Admin Dashboard')

@section('content')

<style>
    body {
        background: linear-gradient(135deg, #eef4ff, #dbe8ff);
        font-family: "Inter", sans-serif;
    }

    /* Page animation */
    .page-animate { animation: fadeIn .7s ease; }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    /* Header */
    .admin-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 25px 30px;
        border-radius: 16px;
        box-shadow: 0 4px 25px rgba(0,0,0,0.1);
        margin-bottom: 25px;
    }

    .admin-header h2 {
        font-weight: 700;
        margin: 0;
        color: white;
    }

    /* Stat cards */
    .stat-card {
        background: white;
        border-radius: 16px;
        padding: 22px;
        box-shadow: 0px 4px 25px rgba(0,0,0,0.08);
        transition: .3s ease;
        border-left: 5px solid #667eea;
    }
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0px 8px 30px rgba(0,0,0,0.1);
    }
    .stat-title {
        color: #555;
        font-size: 14px;
        font-weight: 500;
    }
    .stat-value {
        font-size: 28px;
        font-weight: 700;
        color: #2b2b2b;
    }
    .stat-icon {
        font-size: 36px;
        color: #667eea;
        opacity: 0.8;
    }

    /* Table Card */
    .table-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0px 6px 30px rgba(0,0,0,0.08);
        padding: 0;
        margin-bottom: 25px;
    }

    .table-card .card-header {
        background: #f4f6ff;
        border-radius: 16px 16px 0 0;
        padding: 18px 22px;
        border-bottom: 1px solid #e5e7eb;
    }

    .table-card h5 {
        font-weight: 600;
        color: #2b2b2b;
    }

    /* Tables */
    table {
        color: #374151;
    }
    thead {
        background: #eef2ff;
        color: #1f2937;
        font-weight: 600;
    }
    tbody tr:hover {
        background: #f1f5ff;
    }

    /* Badges */
    .badge-success {
        background: #4ade80 !important;
    }
    .badge-warning {
        background: #fbbf24 !important;
    }
    .badge-danger {
        background: #ef4444 !important;
    }
    .badge-primary {
        background: #667eea !important;
    }

    /* Button */
    .btn-primary {
        background: #667eea;
        border: none;
        border-radius: 8px;
        padding: 6px 14px;
        font-weight: 600;
        transition: .3s;
    }
    .btn-primary:hover {
        background: #5568d3;
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(102,126,234,0.35);
    }

    .quick-actions {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px;
        margin-bottom: 25px;
    }

    .action-card {
        background: white;
        border-radius: 12px;
        padding: 20px;
        text-align: center;
        box-shadow: 0px 4px 20px rgba(0,0,0,0.08);
        transition: .3s;
        text-decoration: none;
        color: inherit;
        display: block;
    }

    .action-card:hover {
        transform: translateY(-5px);
        box-shadow: 0px 8px 30px rgba(0,0,0,0.12);
        color: inherit;
    }

    .action-card i {
        font-size: 32px;
        color: #667eea;
        margin-bottom: 10px;
    }

    .action-card h6 {
        margin: 0;
        font-weight: 600;
        color: #2b2b2b;
    }
</style>


<div class="page-animate">

    <!-- HEADER -->
    <div class="admin-header">
        <h2><i class="fas fa-crown me-2"></i>Super Admin Dashboard</h2>
        <p class="mb-0 mt-2" style="opacity: 0.9;">Full system access and control</p>
    </div>

    <!-- QUICK ACTIONS -->
    <div class="quick-actions">
        <a href="{{ route('admin.users.index') }}" class="action-card">
            <i class="fas fa-users"></i>
            <h6>Manage Users</h6>
        </a>
        <a href="{{ route('admin.rooms.index') }}" class="action-card">
            <i class="fas fa-door-open"></i>
            <h6>Manage Rooms</h6>
        </a>
        <a href="{{ route('admin.bookings.index') }}" class="action-card">
            <i class="fas fa-calendar-check"></i>
            <h6>All Bookings</h6>
        </a>
        <a href="{{ route('bookings.index') }}" class="action-card">
            <i class="fas fa-calendar"></i>
            <h6>My Bookings</h6>
        </a>
    </div>

    <!-- STATS -->
    <div class="row g-3 mb-4">

        <div class="col-md-3">
            <div class="stat-card d-flex justify-content-between align-items-center">
                <div>
                    <p class="stat-title">Total Rooms</p>
                    <div class="stat-value">{{ $totalRooms }}</div>
                </div>
                <i class="fas fa-door-open stat-icon"></i>
            </div>
        </div>

        <div class="col-md-3">
            <div class="stat-card d-flex justify-content-between align-items-center">
                <div>
                    <p class="stat-title">Active Rooms</p>
                    <div class="stat-value">{{ $activeRooms }}</div>
                </div>
                <i class="fas fa-check-circle stat-icon"></i>
            </div>
        </div>

        <div class="col-md-3">
            <div class="stat-card d-flex justify-content-between align-items-center">
                <div>
                    <p class="stat-title">Total Users</p>
                    <div class="stat-value">{{ $totalUsers }}</div>
                </div>
                <i class="fas fa-users stat-icon"></i>
            </div>
        </div>

        <div class="col-md-3">
            <div class="stat-card d-flex justify-content-between align-items-center">
                <div>
                    <p class="stat-title">Admins</p>
                    <div class="stat-value">{{ $totalAdmins }}</div>
                </div>
                <i class="fas fa-user-shield stat-icon"></i>
            </div>
        </div>

        <div class="col-md-3">
            <div class="stat-card d-flex justify-content-between align-items-center">
                <div>
                    <p class="stat-title">Today's Bookings</p>
                    <div class="stat-value">{{ $todayBookings }}</div>
                </div>
                <i class="fas fa-calendar-day stat-icon"></i>
            </div>
        </div>

        <div class="col-md-3">
            <div class="stat-card d-flex justify-content-between align-items-center">
                <div>
                    <p class="stat-title">Upcoming Bookings</p>
                    <div class="stat-value">{{ $upcomingBookings }}</div>
                </div>
                <i class="fas fa-calendar-check stat-icon"></i>
            </div>
        </div>

    </div>

    <div class="row">
        <!-- RECENT BOOKINGS -->
        <div class="col-md-8">
            <div class="table-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5><i class="fas fa-list me-2 text-primary"></i>Recent Bookings</h5>
                    <a href="{{ route('admin.bookings.index') }}" class="btn btn-primary btn-sm">View All</a>
                </div>

                <div class="p-3">
                    @if($recentBookings->count() > 0)
                        <div class="table-responsive">
                            <table class="table align-middle">
                                <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>Room</th>
                                        <th>User</th>
                                        <th>Date & Time</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentBookings as $booking)
                                        <tr>
                                            <td>{{ $booking->title }}</td>
                                            <td>{{ $booking->room->name }}</td>
                                            <td>{{ $booking->user->name }}</td>
                                            <td>
                                                {{ $booking->date->format('M d, Y') }} <br>
                                                <small class="text-muted">
                                                    {{ \Carbon\Carbon::parse($booking->start_time)->format('g:i A') }}
                                                    –
                                                    {{ \Carbon\Carbon::parse($booking->end_time)->format('g:i A') }}
                                                </small>
                                            </td>
                                            <td>
                                                <span class="badge 
                                                    bg-{{ $booking->status === 'confirmed' ? 'success' :
                                                           ($booking->status === 'pending' ? 'warning' : 'danger') }}">
                                                    {{ ucfirst($booking->status) }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted">No recent bookings found.</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- RECENT USERS -->
        <div class="col-md-4">
            <div class="table-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5><i class="fas fa-user-plus me-2 text-primary"></i>Recent Users</h5>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-primary btn-sm">View All</a>
                </div>

                <div class="p-3">
                    @if($recentUsers->count() > 0)
                        <div class="list-group">
                            @foreach($recentUsers as $user)
                                <div class="list-group-item border-0 px-0 py-2">
                                    <div class="d-flex align-items-center">
                                        @if($user->logo)
                                            <img src="{{ asset('storage/' . $user->logo) }}" alt="{{ $user->name }}" 
                                                 style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover; margin-right: 12px;">
                                        @else
                                            <div style="width: 40px; height: 40px; border-radius: 50%; background: #667eea; color: white; display: flex; align-items: center; justify-content: center; margin-right: 12px; font-weight: 600;">
                                                {{ substr($user->name, 0, 1) }}
                                            </div>
                                        @endif
                                        <div class="flex-grow-1">
                                            <div class="fw-semibold">{{ $user->name }}</div>
                                            <small class="text-muted">
    {{ \Illuminate\Support\Str::limit($user->email, 20, '..') }}
</small>

                                        </div>
                                        <span class="badge 
                                            @if($user->role === 'super_admin') bg-danger
                                            @elseif($user->role === 'admin') bg-primary
                                            @else bg-secondary
                                            @endif">
                                            {{ ucfirst(str_replace('_', ' ', $user->role)) }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted">No recent users found.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

</div>

@endsection


