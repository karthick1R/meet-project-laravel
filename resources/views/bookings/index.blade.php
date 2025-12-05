@extends('layouts.app')

@section('title', 'My Bookings')

@section('content')

<style>
/* Smooth Page Animation */
.page-animate {
    animation: fadeIn .6s ease;
}
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to   { opacity: 1; transform: translateY(0); }
}

/* Clean white card */
.clean-card {
    background: #ffffff;
    border-radius: 16px;
    padding: 25px;
    box-shadow: 0px 4px 30px rgba(0,0,0,0.08);
    animation: fadeIn .7s ease;
}

/* Title */
.page-title {
    color: #1f2937;
    font-weight: 700;
    margin-bottom: 20px;
}

/* Table */
.table thead {
    background: #eef4ff;
    color: #1f2937;
    border-radius: 8px;
}

.table th {
    font-size: 14px;
    font-weight: 600;
}

.table tbody tr {
    transition: .25s;
}

.table tbody tr:hover {
    background: #f1f5ff;
}

/* Badges (Zoho-style colors) */
.badge-success {
    background: #16a34a !important;
    color: white;
    font-size: 13px;
}
.badge-warning {
    background: #fbbf24 !important;
    color: white;
    font-size: 13px;
}
.badge-danger {
    background: #dc2626 !important;
    color: white;
    font-size: 13px;
}

/* Cancel Button */
.btn-cancel {
    background: #dc2626;
    border: none;
    border-radius: 8px;
    padding: 6px 12px;
    color: white;
    font-weight: 600;
    transition: .2s;
}
.btn-cancel:hover {
    background: #b91c1c;
    transform: translateY(-2px);
}

/* Empty Message */
.empty-box {
    background: #fff;
    padding: 25px;
    border-radius: 14px;
    box-shadow: 0px 4px 25px rgba(0,0,0,0.08);
    color: #111827;
    font-size: 16px;
}

</style>


<div class="page-animate">

    <h2 class="page-title"><i class="fas fa-calendar-check me-2"></i>My Bookings</h2>

    @if($bookings->count() > 0)

        <div class="clean-card">
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Room</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Status</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($bookings as $booking)
                            <tr id="booking-row-{{ $booking->id }}">
                                <td>{{ $booking->title }}</td>
                                <td>{{ $booking->room->name }}</td>
                                <td>{{ $booking->date->format('M d, Y') }}</td>
                                <td>
                                    {{ \Carbon\Carbon::parse($booking->start_time)->format('g:i A') }} –
                                    {{ \Carbon\Carbon::parse($booking->end_time)->format('g:i A') }}
                                </td>

                                <td>
                                    <span class="badge 
                                        {{ $booking->status === 'confirmed' ? 'badge-success' : 
                                           ($booking->status === 'pending' ? 'badge-warning' : 'badge-danger') }}">
                                        {{ ucfirst($booking->status) }}
                                    </span>
                                </td>

                                <td class="text-center">
                                    @if($booking->status !== 'cancelled')
                                        <button 
                                            class="btn-cancel cancel-btn"
                                            data-id="{{ $booking->id }}">
                                            <i class="fas fa-times me-1"></i>Cancel
                                        </button>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>

                </table>
            </div>
        </div>

    @else

        <div class="empty-box">
            <i class="fas fa-info-circle me-2"></i>
            You have no bookings yet.
        </div>

    @endif

</div>


@push('scripts')
<script>
$(document).ready(function() {

    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });

    $('.cancel-btn').click(function() {
        let id = $(this).data('id');
        let btn = $(this);

        Swal.fire({
            title: "Cancel this booking?",
            text: "This action cannot be undone.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#dc2626",
            cancelButtonColor: "#3b82f6",
            confirmButtonText: "Yes, cancel it!"
        }).then((result) => {

            if (result.isConfirmed) {

                let original = btn.html();
                btn.html('<span class="spinner-border spinner-border-sm"></span>');

                $.post('/bookings/' + id + '/cancel', {}, function(response) {

                    Swal.fire({
                        icon: "success",
                        title: "Cancelled",
                        text: response.message,
                    }).then(() => location.reload());

                }).fail(() => {

                    Swal.fire({
                        icon: "error",
                        title: "Error",
                        text: "Unable to cancel booking."
                    });

                    btn.html(original);
                });
            }

        });
    });

});
</script>
@endpush

@endsection
