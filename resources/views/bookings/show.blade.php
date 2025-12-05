@extends('layouts.app')

@section('title', $booking->title)

@section('content')

<style>
    body { font-family: "Inter", sans-serif; }

    /* Fade animation */
    .fade-in {
        animation: fadeIn .7s ease;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(12px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    /* Page title */
    .page-title {
        font-size: 26px;
        font-weight: 700;
        color: #1f2937;
    }

    /* White Cards - Zoho Style */
    .clean-card {
        background: #ffffff;
        border-radius: 16px;
        padding: 20px;
        border: none;
        box-shadow: 0 4px 35px rgba(0,0,0,0.07);
        animation: fadeIn .7s ease;
    }

    .clean-card-header {
        font-weight: 600;
        font-size: 18px;
        margin-bottom: 15px;
        color: #2b2b2b;
    }

    /* Badges */
    .badge-success { background-color: #22c55e !important; }
    .badge-warning { background-color: #f59e0b !important; }
    .badge-danger  { background-color: #ef4444 !important; }

    /* Attendees List */
    .attendee-item {
        background: #f9f9ff;
        border-radius: 10px;
        padding: 12px;
        margin-bottom: 8px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        transition: .25s ease;
    }
    .attendee-item:hover {
        background: #eef4ff;
    }

    /* Cancel Button */
    .btn-danger-custom {
        background: #ef4444;
        border: none;
        color: white;
        padding: 9px 16px;
        border-radius: 10px;
        font-weight: 600;
        transition: 0.3s;
    }
    .btn-danger-custom:hover {
        background: #dc2626;
        transform: translateY(-3px);
        box-shadow: 0px 6px 16px rgba(220,38,38,0.25);
    }
</style>

<div class="fade-in">

    <!-- HEADER -->
    <div class="row mb-4 align-items-center">
        <div class="col-md-8">
            <h2 class="page-title">
                <i class="fas fa-calendar-check me-2 text-primary"></i>{{ $booking->title }}
            </h2>
        </div>

        <div class="col-md-4 text-end">
            @if($booking->status !== 'cancelled' && $booking->user_id === auth()->id())
            <button type="button" class="btn-danger-custom" onclick="cancelBooking({{ $booking->id }})">
                <i class="fas fa-times me-1"></i> Cancel Booking
            </button>
            @endif
        </div>
    </div>


    <!-- DETAILS SECTIONS -->
    <div class="row">

        <!-- BOOKING INFO -->
        <div class="col-md-6 mb-4">
            <div class="clean-card">
                <div class="clean-card-header">
                    <i class="fas fa-info-circle me-2"></i>Booking Information
                </div>

                <p><strong>Room:</strong> {{ $booking->room->name }}</p>
                <p><strong>Date:</strong> {{ $booking->date->format('F d, Y') }}</p>
                <p><strong>Time:</strong> 
                    {{ \Carbon\Carbon::parse($booking->start_time)->format('g:i A') }}
                    –
                    {{ \Carbon\Carbon::parse($booking->end_time)->format('g:i A') }}
                </p>

                <p><strong>Status:</strong>
                    <span class="badge 
                        bg-{{ $booking->status === 'confirmed' ? 'success' : 
                            ($booking->status === 'pending' ? 'warning' : 'danger') }}">
                        {{ ucfirst($booking->status) }}
                    </span>
                </p>

                @if($booking->description)
                    <p><strong>Description:</strong> {{ $booking->description }}</p>
                @endif

                @if($booking->recurrence !== 'none')
                    <p><strong>Recurrence:</strong> {{ ucfirst($booking->recurrence) }}</p>

                    @if($booking->recurrence_end_date)
                        <p><strong>Recurrence Ends:</strong> {{ $booking->recurrence_end_date->format('F d, Y') }}</p>
                    @endif
                @endif
            </div>
        </div>


        <!-- ROOM INFO -->
        <div class="col-md-6 mb-4">
            <div class="clean-card">
                <div class="clean-card-header">
                    <i class="fas fa-door-open me-2"></i>Room Details
                </div>

                <p><strong>Capacity:</strong> {{ $booking->room->capacity }} people</p>
                <p><strong>Location:</strong> {{ $booking->room->location }}</p>

                @if($booking->room->amenities)
                    <p><strong>Amenities:</strong></p>
                    <ul>
                        @foreach($booking->room->amenities as $amenity)
                            <li>{{ $amenity }}</li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>

    </div>


    <!-- ATTENDEE LIST -->
    @if($booking->invitations->count() > 0)
    <div class="clean-card mt-3">
        <div class="clean-card-header">
            <i class="fas fa-users me-2"></i>Attendees
        </div>

        @foreach($booking->invitations as $invitation)
            <div class="attendee-item">
                <span>{{ $invitation->email }}</span>

                <span class="badge 
                    bg-{{ $invitation->status === 'accepted' ? 'success' : 
                        ($invitation->status === 'declined' ? 'danger' : 'warning') }}">
                    {{ ucfirst($invitation->status) }}
                </span>
            </div>
        @endforeach
    </div>
    @endif

</div>


@push('scripts')
<script>
    function cancelBooking(id) {
        Swal.fire({
            title: 'Cancel Booking?',
            text: "This action cannot be undone.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#3b82f6',
            confirmButtonText: 'Yes, cancel it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post(`/bookings/${id}/cancel`, {}, function (response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Cancelled!',
                        text: response.message
                    }).then(() => {
                        window.location.href = "{{ route('bookings.index') }}";
                    });
                }).fail(() => {
                    Swal.fire('Error', 'Failed to cancel booking.', 'error');
                });
            }
        });
    }
</script>
@endpush

@endsection
