@extends('layouts.app')

@section('title', 'Manage Rooms')

@section('content')

<style>
    body {
        font-family: "Inter", sans-serif;
    }

    .page-title {
        font-size: 28px;
        font-weight: 700;
        color: #1f2937;
    }

    .btn-add {
        background: linear-gradient(135deg, #3b7df0, #5a8bff);
        color: white;
        padding: 10px 18px;
        border-radius: 10px;
        font-weight: 600;
        border: none;
        transition: 0.3s ease;
    }

    .btn-add:hover {
        transform: translateY(-3px);
        box-shadow: 0px 10px 25px rgba(59, 125, 240, 0.3);
    }

    /* Room Card */
    .room-card {
        background: white;
        border-radius: 14px;
        padding: 18px;
        box-shadow: 0 6px 20px rgba(0,0,0,0.07);
        border: 1px solid #e5e7eb;
        transition: .25s ease;
    }

    .room-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 24px rgba(0,0,0,0.10);
    }

    .room-header {
        padding: 14px;
        border-radius: 12px;
        color: #fff;
        font-weight: 600;
        margin-bottom: 12px;
    }

    .room-color-box {
        width: 18px;
        height: 18px;
        border-radius: 5px;
        display: inline-block;
        border: 1px solid #e5e7eb;
    }

    /* Buttons */
    .btn-edit {
        background: #3b82f6;
        color: white;
        font-weight: 600;
        border-radius: 8px;
        border: none;
        padding: 6px 14px;
    }
    .btn-edit:hover {
        background: #2563eb;
    }

    .btn-delete {
        background: #ef4444;
        color: white;
        font-weight: 600;
        border-radius: 8px;
        border: none;
        padding: 6px 14px;
    }
    .btn-delete:hover {
        background: #dc2626;
    }

    /* Modal */
    .modal-content {
        border-radius: 14px;
        border: none;
        box-shadow: 0 6px 25px rgba(0,0,0,0.15);
    }

    .modal-header {
        background: linear-gradient(135deg, #3b7df0, #5a8bff);
        color: white;
        border-radius: 14px 14px 0 0;
    }

    .form-control, .form-select {
        border-radius: 10px;
        border: 1px solid #d1d5db;
    }
</style>


<div>

    <!-- HEADER -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="page-title"><i class="fas fa-door-open me-2"></i>Manage Rooms</h2>

        <button class="btn-add" data-bs-toggle="modal" data-bs-target="#roomModal" onclick="resetRoomForm()">
            <i class="fas fa-plus me-2"></i>Add Room
        </button>
    </div>


    <!-- ROOM GRID -->
    <div class="row">
        @foreach($rooms as $room)
        <div class="col-md-4 mb-4">
            <div class="room-card">

                <div class="room-header" style="background: {{ $room->color }}">
                    {{ $room->name }}
                </div>

                <p class="text-gray-600">{{ $room->description }}</p>
                <p><i class="fas fa-users me-1"></i> <strong>Capacity:</strong> {{ $room->capacity }}</p>
                <p><i class="fas fa-map-marker-alt me-1"></i>{{ $room->location }}</p>

                @if($room->amenities)
                <p>
                    <strong>Amenities:</strong><br>
                    @foreach($room->amenities as $amenity)
                        <span class="badge bg-gray-200 text-dark">{{ $amenity }}</span>
                    @endforeach
                </p>
                @endif
                
                <p class="mt-2">
                    <span class="badge bg-{{ $room->is_active ? 'success' : 'secondary' }}">
                        {{ $room->is_active ? 'Active' : 'Inactive' }}
                    </span>
                    <span class="room-color-box ms-2" style="background: {{ $room->color }}"></span>
                </p>

                <div class="mt-3 d-flex justify-content-between">
                    <button class="btn-edit"
                        onclick="editRoom({{ $room->id }}, 
                        '{{ $room->name }}', 
                        '{{ $room->description }}', 
                        {{ $room->capacity }}, 
                        '{{ $room->location }}', 
                        '{{ implode(',', $room->amenities ?? []) }}', 
                        '{{ $room->color }}', 
                        {{ $room->is_active ? 'true' : 'false' }})">
                        <i class="fas fa-edit me-1"></i>Edit
                    </button>

                    <button class="btn-delete" onclick="deleteRoom({{ $room->id }})">
                        <i class="fas fa-trash me-1"></i>Delete
                    </button>
                </div>

            </div>
        </div>
        @endforeach
    </div>

</div>


<!-- ROOM MODAL -->
<div class="modal fade" id="roomModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h5 id="roomModalTitle">Add Room</h5>
                <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <form id="roomForm">
                <input type="hidden" id="room_id">

                <div class="modal-body">

                    <label class="form-label">Room Name *</label>
                    <input type="text" class="form-control mb-3" id="room_name" required>

                    <label class="form-label">Description</label>
                    <textarea class="form-control mb-3" id="room_description"></textarea>

                    <label class="form-label">Capacity *</label>
                    <input type="number" class="form-control mb-3" id="room_capacity" min="1" required>

                    <label class="form-label">Location</label>
                    <input type="text" class="form-control mb-3" id="room_location">

                    <label class="form-label">Amenities (comma separated)</label>
                    <input type="text" class="form-control mb-3" id="room_amenities">

                    <label class="form-label">Calendar Color</label>
                    <input type="color" id="room_color" class="form-control form-control-color mb-3">

                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="room_is_active">
                        <label class="form-check-label">Active</label>
                    </div>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-light" data-bs-dismiss="modal">Close</button>
                    <button class="btn-add">Save Room</button>
                </div>

            </form>

        </div>
    </div>
</div>


@push('scripts')
<script>
function resetRoomForm() {
    $('#roomForm')[0].reset();
    $('#room_id').val('');
    $('#roomModalTitle').text('Add Room');
    $('#room_color').val('#3b7df0');
    $('#room_is_active').prop('checked', true);
}

function editRoom(id, name, desc, cap, loc, amenities, color, active) {
    $('#room_id').val(id);
    $('#roomModalTitle').text('Edit Room');

    $('#room_name').val(name);
    $('#room_description').val(desc);
    $('#room_capacity').val(cap);
    $('#room_location').val(loc);
    $('#room_amenities').val(amenities);
    $('#room_color').val(color);
    $('#room_is_active').prop('checked', active);

    $('#roomModal').modal('show');
}

function deleteRoom(id) {
    Swal.fire({
        title: "Delete Room?",
        text: "This action cannot be undone.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#ef4444",
        cancelButtonColor: "#3b82f6",
        confirmButtonText: "Delete"
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '/admin/rooms/' + id,
                method: 'DELETE',
                success: function(response) {
                    Swal.fire("Deleted!", response.message, "success")
                        .then(() => location.reload());
                }
            });
        }
    });
}

$("#roomForm").submit(function(e) {
    e.preventDefault();

    let id = $("#room_id").val();
    let url = id ? '/admin/rooms/' + id : '/admin/rooms';
    let method = id ? 'PUT' : 'POST';

    $.ajax({
        url: url,
        method: method,
        data: {
            name: $("#room_name").val(),
            description: $("#room_description").val(),
            capacity: $("#room_capacity").val(),
            location: $("#room_location").val(),
            amenities: $("#room_amenities").val(),
            color: $("#room_color").val(),
            is_active: $("#room_is_active").is(":checked") ? 1 : 0,
            _token: $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            Swal.fire("Saved!", response.message, "success")
                .then(() => location.reload());
        }
    });
});
</script>
@endpush

@endsection
