@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

<style>
    body {
        background: linear-gradient(135deg, #eef4ff, #dbe8ff);
        font-family: "Inter", sans-serif;
    }

    .page-title {
        font-size: 28px;
        font-weight: 700;
        color: #2b2b2b;
    }

    .btn-create {
        background: linear-gradient(135deg, #3b7df0, #5a8bff);
        color: white;
        padding: 10px 18px;
        border-radius: 10px;
        font-weight: 600;
        border: none;
        transition: 0.3s ease;
    }

    .btn-create:hover {
        transform: translateY(-3px);
        box-shadow: 0px 8px 20px rgba(59, 125, 240, 0.25);
    }

    /* White Zoho-style card */
    .clean-card {
        background: white;
        border-radius: 16px;
        padding: 20px;
        box-shadow: 0px 4px 30px rgba(0, 0, 0, 0.08);
        animation: fadeIn 0.7s;
        border: none;
    }

    .clean-card-header {
        padding: 12px 0;
        border-bottom: 1px solid #e7e7e7;
        margin-bottom: 18px;
        font-size: 18px;
        font-weight: 600;
        color: #2b2b2b;
    }

    /* List */
    .list-item {
        background: #f7f9ff;
        padding: 14px;
        border-radius: 10px;
        margin-bottom: 10px;
        transition: 0.25s ease;
        border: 1px solid #e7edff;
    }

    .list-item:hover {
        background: #eef4ff;
        transform: translateX(4px);
    }

    .list-item h6 {
        font-weight: 600;
        color: #2b2b2b;
    }

    .list-item small, .list-item p {
        color: #4b5563;
    }

    /* Calendar Fix */
    .calendar-box {
        padding: 10px;
        background: #f8fbff;
        border-radius: 14px;
        border: 1px solid #e8ecf7;
    }

    .fc .fc-toolbar-title {
        color: #1f2937;
        font-weight: 700;
        font-size: 23px;
    }

    .fc-button-primary {
        background: #3b7df0 !important;
        border: none !important;
        border-radius: 6px !important;
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
        background: linear-gradient(135deg, #ff4b1f, #ff9068) !important;
        border-radius: 6px;
        color: white !important;
        border: none;
    }

    @keyframes fadeIn {
        from {opacity: 0; transform: translateY(12px);}
        to   {opacity: 1; transform: translateY(0);}
    }
</style>

<!-- HEADER -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="page-title"><i class="fas fa-calendar-alt me-2"></i>Dashboard</h2>

    <button class="btn-create" data-bs-toggle="modal" data-bs-target="#bookingModal">
        <i class="fas fa-plus me-2"></i>New Booking
    </button>
</div>


<!-- CALENDAR -->
<div class="row mb-4">
    <div class="col-md-12">
        <div class="clean-card">
            <div class="clean-card-header">
                <i class="fas fa-calendar me-2"></i>Calendar
            </div>

            <div class="calendar-box">
                <div id="calendar"></div>
            </div>
        </div>
    </div>
</div>


<div class="row">

    <!-- ROOMS LIST -->
    <div class="col-md-6">
        <div class="clean-card">
            <div class="clean-card-header">
                <i class="fas fa-door-open me-2"></i>Available Rooms
            </div>

            @if($rooms->count())
                @foreach($rooms as $room)
                <a href="{{ route('rooms.show', $room->id) }}" class="text-decoration-none">
                    <div class="list-item">
                        <div class="d-flex justify-content-between">
                            <h6>{{ $room->name }}</h6>
                            <small>Capacity: {{ $room->capacity }}</small>
                        </div>
                        <p class="mb-1">{{ $room->description }}</p>
                        <small><i class="fas fa-map-marker-alt me-1"></i>{{ $room->location }}</small>
                    </div>
                </a>
                @endforeach
            @else
                <p>No rooms available.</p>
            @endif
        </div>
    </div>


    <!-- UPCOMING BOOKINGS -->
    <div class="col-md-6">
        <div class="clean-card">
            <div class="clean-card-header">
                <i class="fas fa-clock me-2"></i>Upcoming Bookings
            </div>

            @if($upcomingBookings->count())
                @foreach($upcomingBookings as $booking)
                <div class="list-item">
                    <div class="d-flex justify-content-between">
                        <h6>{{ $booking->title }}</h6>
                        <small>{{ $booking->date->format('M d, Y') }}</small>
                    </div>
                    <p class="mb-1">
                        <i class="fas fa-door-open me-1"></i>{{ $booking->room->name }} <br>
                        <i class="fas fa-clock me-1"></i>
                        {{ \Carbon\Carbon::parse($booking->start_time)->format('g:i A') }} -
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


<!-- BOOKING MODAL -->
@include('bookings.modal')


@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {

    var calendarEl = document.getElementById('calendar');

    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        height: 650,

        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },

        events: {
            url: '{{ route("bookings.calendar") }}',
            method: 'GET',
            failure: function() {
                Swal.fire('Error', 'Failed to load calendar events', 'error');
            }
        },

        dateClick: function(info) {
            $('#bookingModal').modal('show');
            $('#booking_date').val(info.dateStr);
        },

        eventClick: function(info) {
            Swal.fire({
                title: info.event.title,
                html:
                    '<p><strong>Room:</strong> ' + info.event.extendedProps.room_name + '</p>' +
                    '<p><strong>Time:</strong> ' +
                    info.event.start.toLocaleString() + '</p>' +
                    (info.event.extendedProps.description
                        ? '<p>' + info.event.extendedProps.description + '</p>'
                        : ''),
                icon: 'info',
            });
        }

    });

    calendar.render();
});
</script>
@endpush

@endsection
