<!-- CONFETTI -->
<script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>

<style>
/* Toast */
#successToast {
    position: fixed;
    top: 20px;
    right: 20px;
    min-width: 280px;
    padding: 14px 18px;
    background: #ffffff;
    border-left: 5px solid #22c55e;
    border-radius: 10px;
    box-shadow: 0px 8px 25px rgba(0,0,0,0.15);
    font-family: "Inter", sans-serif;
    display: none;
    animation: fadeSlideIn .4s ease forwards;
    z-index: 99999;
}

#successToast i {
    color: #22c55e;
    margin-right: 10px;
    font-size: 20px;
}

@keyframes fadeSlideIn {
    from { opacity: 0; transform: translateX(40px); }
    to   { opacity: 1; transform: translateX(0); }
}

@keyframes fadeSlideOut {
    from { opacity: 1; transform: translateX(0); }
    to   { opacity: 0; transform: translateX(40px); }
}
</style>

<div id="successToast">
    <i class="fas fa-check-circle"></i>
    <strong id="successToastMsg">Success Message</strong>
</div>

<script>
function showSuccessPopup(message) {
    let toast = document.getElementById("successToast");
    document.getElementById("successToastMsg").innerHTML = message;

    toast.style.display = "block";
    toast.style.animation = "fadeSlideIn .4s ease";

    setTimeout(() => {
        toast.style.animation = "fadeSlideOut .4s ease";
        setTimeout(() => toast.style.display = "none", 400);
    }, 2500);
}

function fireConfetti() {
    const end = Date.now() + 900;
    (function frame() {
        confetti({
            particleCount: 8,
            spread: 360,
            startVelocity: 45,
            origin: { x: Math.random(), y: Math.random() - 0.2 }
        });
        if (Date.now() < end) requestAnimationFrame(frame);
    })();
}
</script>



<!-- BOOKING MODAL -->
<div class="modal fade" id="bookingModal" tabindex="-1">
    <div class="modal-dialog modal-lg">

        <div class="modal-content" style="
            border-radius:16px;
            background:#ffffff;
            border:none;
            box-shadow:0px 10px 40px rgba(0,0,0,0.15);
            font-family:Inter,sans-serif;
        ">

            <!-- HEADER -->
            <div class="modal-header" style="background:#3b7df0; color:white; border-radius:16px 16px 0 0;">
                <h5 class="modal-title">
                    <i class="fas fa-calendar-plus me-2"></i>Create Booking
                </h5>
                <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>


            <form id="bookingForm">

                <div class="modal-body">

                    <!-- Room -->
                    <div class="mb-3">
                        <label class="form-label">Room *</label>
                        <select class="form-select" id="booking_room_id" name="room_id" required>
                            <option value="">Select a room</option>
                            @foreach(\App\Models\Room::where('is_active', true)->get() as $room)
                                <option value="{{ $room->id }}">{{ $room->name }} ({{ $room->capacity }} People)</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Title -->
                    <div class="mb-3">
                        <label class="form-label">Title *</label>
                        <input type="text" class="form-control" id="booking_title" name="title" required>
                    </div>

                    <!-- Description -->
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" id="booking_description" name="description" rows="3"></textarea>
                    </div>

                    <!-- Date & Time -->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Date *</label>
                            <input type="date" class="form-control" id="booking_date" name="date" min="{{ date('Y-m-d') }}" required>
                        </div>

                        <div class="col-md-3 mb-3">
                            <label class="form-label">Start Time *</label>
                            <input type="time" class="form-control" id="booking_start_time" name="start_time" required>
                        </div>

                        <div class="col-md-3 mb-3">
                            <label class="form-label">End Time *</label>
                            <input type="time" class="form-control" id="booking_end_time" name="end_time" required>
                        </div>
                    </div>

                    <!-- Recurrence -->
                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label">Recurrence</label>
                            <select class="form-select" id="booking_recurrence" name="recurrence">
                                <option value="none">None</option>
                                <option value="daily">Daily</option>
                                <option value="weekly">Weekly</option>
                                <option value="monthly">Monthly</option>
                            </select>
                        </div>

                        <div class="col-md-6" id="recurrence_end_date_container" style="display:none;">
                            <label class="form-label">End Date</label>
                            <input type="date" class="form-control" id="booking_recurrence_end_date" name="recurrence_end_date">
                        </div>
                    </div>

                    <!-- Attendees -->
                    <div class="mt-3">
                        <label class="form-label">Attendees</label>
                        <input type="text" class="form-control" id="booking_attendees" name="attendees"
                               placeholder="email1@example.com, email2@example.com">
                    </div>

                    <!-- Availability -->
                    <div id="availability_check_result"
                         class="alert mt-3"
                         style="display:none; border-radius:10px;">
                    </div>

                </div>


                <div class="modal-footer">

                    <button class="btn btn-light border" data-bs-dismiss="modal">Close</button>

                    <button type="button" id="checkAvailabilityBtn" class="btn btn-secondary">
                        <i class="fas fa-search me-1"></i>Check Availability
                    </button>

                    <button type="submit" id="createBookingBtn" class="btn btn-primary">
                        <span id="createBookingText"><i class="fas fa-save me-1"></i>Create</span>
                        <span class="spinner-border spinner-border-sm" id="createBookingSpinner" style="display:none;"></span>
                    </button>

                </div>

            </form>

        </div>
    </div>
</div>



@push('scripts')
<script>
$(document).ready(function () {

    $('#booking_recurrence').change(function () {
        $('#recurrence_end_date_container').toggle($(this).val() !== 'none');
    });

    $('#checkAvailabilityBtn').click(function () {
        let data = {
            room_id: $('#booking_room_id').val(),
            date: $('#booking_date').val(),
            start_time: $('#booking_start_time').val(),
            end_time: $('#booking_end_time').val()
        };

        $.post('{{ route("bookings.check-availability") }}', data)
            .done(function (response) {
                let box = $('#availability_check_result');

                if (response.available) {
                    box
                        .removeClass('alert-danger')
                        .addClass('alert-success')
                        .text(response.message)
                        .fadeIn();
                } else {
                    box
                        .removeClass('alert-success')
                        .addClass('alert-danger')
                        .text(response.message)
                        .fadeIn();
                }
            });
    });

    // Create booking
    $('#bookingForm').submit(function (e) {
        e.preventDefault();

        $('#createBookingSpinner').show();
        $('#createBookingBtn').prop('disabled', true);
        $('#createBookingText').text("Creating...");

        $.post('{{ route("bookings.store") }}', $(this).serialize())
            .done(function (response) {
                fireConfetti();
                showSuccessPopup("Booking Created Successfully!");
                setTimeout(() => location.reload(), 1200);
            })
            .fail(function () {
                showSuccessPopup("Something went wrong!");
            });
    });

});
</script>
@endpush

