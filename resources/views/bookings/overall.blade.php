@extends('layouts.app')

@section('title', 'All Bookings')

@section('content')

<style>
    body {
        font-family: "Inter", sans-serif;
        background: #f5f8ff;
    }

    /* PAGE TITLE */
    .page-title {
        font-size: 26px;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 25px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .page-title i {
        color: #3b7df0;
    }

    /* FILTER CARD */
    .filter-card {
        background: #ffffff;
        border-radius: 14px;
        padding: 20px;
        box-shadow: 0px 4px 18px rgba(0,0,0,0.07);
        border: 1px solid #e2e8f0;
        margin-bottom: 25px;
        animation: fadeIn .5s ease;
    }

    /* TABLE CARD */
    .table-container {
        background: #ffffff;
        border-radius: 14px;
        padding: 15px;
        border: 1px solid #e2e8f0;
        box-shadow: 0px 4px 20px rgba(0,0,0,0.06);
        animation: fadeIn .7s ease;
    }

    table thead {
        background: #f0f4ff;
        color: #1e293b;
        font-weight: 600;
    }

    table tbody tr {
        transition: 0.25s;
        color: #1e293b;
    }

    table tbody tr:hover {
        background: #eef4ff;
    }

    /* BADGES */
    .badge-success {
        background: #16a34a !important;
    }
    .badge-warning {
        background: #eab308 !important;
    }
    .badge-danger {
        background: #dc2626 !important;
    }

    /* FILTER BUTTON */
    .btn-primary {
        background: #3b7df0;
        border: none;
        border-radius: 10px;
        font-weight: 600;
        transition: 0.3s;
        color: white;
    }
    .btn-primary:hover {
        background: #2563eb;
    }

    /* VIEW BUTTON */
    .btn-info {
        background: #06b6d4;
        border-radius: 8px;
        border: none;
        color: white;
        transition: 0.3s;
        padding: 6px 12px;
    }
    .btn-info:hover {
        background: #0ea5e9;
    }

    /* PAGINATION */
    .pagination .page-item .page-link {
        border-radius: 8px;
        border: none;
        background: #e2e8f0;
        color: #1e293b;
    }
    .pagination .page-item.active .page-link {
        background: #3b7df0;
        color: white;
    }

    @keyframes fadeIn {
        from { opacity:0; transform: translateY(10px); }
        to   { opacity:1; transform: translateY(0); }
    }
</style>


<div class="page-animate">

    <!-- PAGE TITLE -->
    <h2 class="page-title"><i class="fas fa-list"></i> All Bookings</h2>

    <!-- FILTER CARD -->
    <div class="filter-card">
        <form method="GET" action="{{ route('bookings.overall') }}" class="row g-3">

            <div class="col-md-3">
                <label class="form-label">Room</label>
                <select name="room_id" class="form-select">
                    <option value="">All Rooms</option>
                    @foreach($rooms as $room)
                        <option value="{{ $room->id }}" {{ request('room_id') == $room->id ? 'selected' : '' }}>
                            {{ $room->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label">Date</label>
                <input type="date" name="date" class="form-control" value="{{ request('date') }}">
            </div>

            <div class="col-md-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">All Statuses</option>
                    <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>

            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-filter me-1"></i> Filter
                </button>
            </div>

        </form>
    </div>

    <!-- TABLE CARD -->
    <div class="table-container">

        <div class="table-responsive">
            <table class="table table-hover align-middle">

                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Room</th>
                        <th>User</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Status</th>
                        <th>View</th>
                    </tr>
                </thead>

                <tbody>
                @forelse($bookings as $booking)
                    <tr>
                        <td>{{ $booking->title }}</td>
                        <td>{{ $booking->room->name  }}</td>
                        <td>
                            <strong>{{ $booking->user->name ?? 'N/A' }}</strong>
                            <br>
                            <small>{{ $booking->user->email ?? 'N/A' }}</small>
                        </td>
                        <td>{{ $booking->date->format('M d, Y') }}</td>

                        <td>
                            {{ \Carbon\Carbon::parse($booking->start_time)->format('g:i A') }}
                            –
                            {{ \Carbon\Carbon::parse($booking->end_time)->format('g:i A') }}
                        </td>

                        <td>
                            <span class="badge 
                                bg-{{ $booking->status === 'confirmed' ? 'success' :
                                   ($booking->status === 'pending' ? 'warning' : 'danger') }}">
                                {{ ucfirst($booking->status) }}
                            </span>
                        </td>

                        <td>
                            <a href="{{ route('bookings.show', $booking->id) }}" class="btn btn-info btn-sm">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>

                @empty
                    <tr>
                        <td colspan="7" class="text-center text-secondary">No bookings found.</td>
                    </tr>
                @endforelse
                </tbody>

            </table>
        </div>

    </div>

    <!-- PAGINATION -->
    <div class="mt-3 d-flex justify-content-center">
        {{ $bookings->links() }}
    </div>

</div>

@endsection
