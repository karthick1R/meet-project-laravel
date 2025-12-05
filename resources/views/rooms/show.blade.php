@extends('layouts.app')

@section('title', $room->name)

@section('content')

<style>
    body {
        font-family: "Inter", sans-serif;
    }

    /* PAGE ANIMATION */
    .page-animate {
        animation: fadeIn .7s ease;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    /* PAGE HEADER */
    .page-header-box {
        background: white;
        padding: 25px;
        border-radius: 16px;
        box-shadow: 0px 6px 25px rgba(0,0,0,0.06);
        border-left: 6px solid #3b7df0;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .page-header-box h2 {
        font-size: 26px;
        font-weight: 700;
        color: #1f2937;
    }
    .page-header-box p {
        color: #6b7280;
        margin-top: 4px;
    }

    /* BUTTON */
    .btn-book {
        background: linear-gradient(135deg, #3b7df0, #5a8bff);
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 12px;
        font-weight: 600;
        transition: 0.3s;
    }
    .btn-book:hover {
        transform: translateY(-3px);
        box-shadow: 0px 10px 22px rgba(59,125,240,0.25);
    }

    /* CARD */
    .clean-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0px 5px 18px rgba(0,0,0,0.06);
        margin-bottom: 25px;
        animation: fadeIn .7s ease;
    }

    .clean-card .card-header {
        background: #f3f7ff;
        padding: 15px 20px;
        border-radius: 16px 16px 0 0;
        border-bottom: 1px solid #e5e7eb;
        font-weight: 600;
        color: #1e3a8a;
    }

    .clean-card p, .clean-card li, .clean-card small {
        color: #444 !important;
    }

    /* LIST ITEMS */
    .list-group-item {
        background: #f8fafc;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        margin-bottom: 10px;
        padding: 15px;
        transition: 0.2s ease;
    }
    .list-group-item:hover {
        background: #eef4ff;
        transform: translateX(4px);
    }

    /* CALENDAR */
    .calendar-container {
        padding: 10px;
        background: white;
        border-radius: 16px;
        box-shadow: 0px 5px 18px rgba(0,0,0,0.06);
    }

    .fc .fc-toolbar-title {
        color: #1f2937;
        font-weight: 700;
        font-size: 22px;
    }
    .fc-button-primary {
        background: #3b7df0 !important;
        border: none !important;
    }
    .fc-col-header-cell-cushion {
        color: #3b7df0 !important;
        font-weight: 600;
    }
    .fc-daygrid-day-number {
        color: #1f2937 !important;
        font-weight: 600;
    }
    .fc-event {
        background: linear-gradient(135deg, #ff7b54, #ffb199) !important;
        border: none !important;
        color: white !important;
        border-radius: 8px;
        padding: 4px;
    }
</style>


<div class="page-animate">

    <!-- PAGE HEADER -->
    <div class="page-header-box mb-4">
        <div>
            <h2><i class="fas fa-door-open me-2 text-primary"></i>{{ $room->name }}</h2>
            <p>{{ $room->description }}</p>
        </div>

        <button class="btn-book" data-bs-toggle="modal" data-bs-target="#bookingModal">
            <i class="fas fa-calendar-plus me-2"></i>Book This Room
        </button>
    </div>


    <div class="row">

        <!-- ROOM DETAILS -->
        <div class="col-md-6">
            <div class="clean-card">
                <div class="card-header">
                    <i class="fas fa-info-circle me-2"></i>Room Details
                </div>
                <div class="card-body">
                    <p><i class="fas fa-users me-2"></i><strong>Capacity:</strong> {{ $room->capacity }} people</p>
                    <p><i class="fas fa-map-marker-alt me-2"></i><strong>Location:</strong> {{ $room->location }}</p>

                    @if($room->amenities)
                        <p><strong>Amenities:</strong></p>
                        <ul>
                            @foreach($room->amenities as $amenity)
                                <li>{{ $amenity }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>

        <!-- UPCOMING BOOKINGS -->
        <div class="col-md-6">
            <div class="clean-card">
                <div class="card-header">
                    <i class="fas fa-clock me-2"></i>Upcoming Bookings
                </div>
                <div class="card-body">

                    @if($room->bookings->count())
                        @foreach($room->bookings->take(5) as $booking)
                            <div class="list-group-item">
                                <div class="d-flex justify-content-between">
                                    <h6 class="fw-bold">{{ $booking->title }}</h6>
                                    <small>{{ $booking->date->format('M d, Y') }}</small>
                                </div>
                                <p class="mb-0">
                                    {{ \Carbon\Carbon::parse($booking->start_time)->format('g:i A') }} –
                                    {{ \Carbon\Carbon::parse($booking->end_time)->format('g:i A') }}
                                </p>
                            </div>
                        @endforeach
                    @else
                        <p>No upcoming bookings.</p>
                    @endif

                </div>
            </div>
        </div>

    </div>


    <!-- CALENDAR -->
    <div class="clean-card mt-4">
        <div class="card-header">
            <i class="fas fa-calendar me-2"></i>Calendar
        </div>
        <div class="card-body">
            <div id="calendar" class="calendar-container"></div>
        </div>
    </div>

</div>


<!-- Booking Modal -->
@include('bookings.modal', ['room_id' => $room->id])


@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {

    var calendar = new FullCalendar.Calendar(document.getElementById('calendar'), {
        initialView: 'dayGridMonth',

        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },

        events: {
            url: '{{ route("bookings.calendar") }}',
            method: 'GET',
            data: { room_id: {{ $room->id }} }
        },

        eventClick: function(info) {
            Swal.fire({
                title: info.event.title,
                html: `
                    <p><strong>Room:</strong> ${info.event.extendedProps.room_name}</p>
                    <p><strong>Time:</strong> ${info.event.start.toLocaleString()}</p>
                `,
                icon: "info"
            });
        },

        dateClick: function(info) {
            $('#bookingModal').modal('show');
            $('#booking_room_id').val({{ $room->id }});
            $('#booking_date').val(info.dateStr);
        }

    });

    calendar.render();
});
</script>
@endpush

@endsection
