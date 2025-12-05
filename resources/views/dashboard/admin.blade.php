@extends('layouts.app')

@section('title', 'Admin Dashboard')

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
        background: white;
        padding: 20px 30px;
        border-radius: 16px;
        box-shadow: 0 4px 25px rgba(0,0,0,0.06);
        margin-bottom: 25px;
    }

    .admin-header h2 {
        font-weight: 700;
        color: #2b2b2b;
        margin: 0;
    }

    /* Stat cards */
    .stat-card {
        background: white;
        border-radius: 16px;
        padding: 22px;
        box-shadow: 0px 4px 25px rgba(0,0,0,0.08);
        transition: .3s ease;
        border-left: 5px solid #3b7df0;
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
        font-size: 26px;
        font-weight: 700;
        color: #2b2b2b;
    }
    .stat-icon {
        font-size: 32px;
        color: #3b7df0;
        opacity: 0.8;
    }

    /* Table Card */
    .table-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0px 6px 30px rgba(0,0,0,0.08);
        padding: 0;
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

    /* Button */
    .btn-primary {
        background: #3b7df0;
        border: none;
        border-radius: 8px;
        padding: 6px 14px;
        font-weight: 600;
        transition: .3s;
    }
    .btn-primary:hover {
        background: #2563eb;
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(59,125,240,0.35);
    }
</style>


<div class="page-animate">

    <!-- HEADER -->
    <div class="admin-header">
        <h2><i class="fas fa-tachometer-alt me-2 text-primary"></i>Admin Dashboard</h2>
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


    <!-- RECENT BOOKINGS -->
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

@endsection
