@extends('layouts.app')

@section('title', 'Rooms')

@section('content')

<style>
    .page-title {
        font-size: 28px;
        font-weight: 700;
        color: #2b2b2b;
        margin-bottom: 25px;
        animation: fadeIn .5s ease;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(12px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    /* Room Card */
    .room-card {
        background: white;
        border-radius: 16px;
        padding: 0;
        overflow: hidden;
        box-shadow: 0px 6px 24px rgba(0,0,0,0.08);
        transition: 0.3s ease;
        animation: fadeUp .6s;
    }

    @keyframes fadeUp {
        from { opacity: 0; transform: translateY(25px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    .room-card:hover {
        transform: translateY(-6px);
        box-shadow: 0px 10px 28px rgba(0,0,0,0.12);
    }

    /* Header Color */
    .room-header {
        padding: 15px;
        color: white;
    }

    /* Body */
    .room-body {
        padding: 18px;
        color: #1f2937;
    }

    .room-body .label {
        font-weight: 600;
        color: #374151;
    }

    /* Amenities */
    .badge-amenity {
        background: #eef2ff;
        color: #4f46e5;
        font-weight: 600;
        border-radius: 8px;
        padding: 6px 10px;
        font-size: 12px;
    }

    /* Book Button */
    .btn-book {
        background: linear-gradient(135deg, #3b7df0, #5a8bff);
        border-radius: 10px;
        color: white;
        width: 100%;
        font-weight: 600;
        padding: 10px;
        border: none;
        transition: 0.3s ease;
    }

    .btn-book:hover {
        transform: translateY(-3px);
        box-shadow: 0px 6px 18px rgba(59, 125, 240, 0.3);
    }

    /* Empty State */
    .empty-state {
        background: #f8fbff;
        border-radius: 16px;
        padding: 20px;
        text-align: center;
        color: #374151;
        font-weight: 500;
        box-shadow: 0 5px 20px rgba(0,0,0,0.06);
        animation: fadeIn .6s ease;
    }

</style>

<h2 class="page-title"><i class="fas fa-door-open me-2"></i>Available Rooms</h2>

<div class="row">
    @forelse($rooms as $room)
        <div class="col-md-4 mb-4">

            <div class="room-card">

                <!-- Room Header (Color based on DB) -->
                <div class="room-header" style="background-color: {{ $room->color }};">
                    <h5 class="mb-0 fw-bold">{{ $room->name }}</h5>
                </div>

                <!-- Room Body -->
                <div class="room-body">
                    <p class="text-muted mb-2">{{ $room->description }}</p>

                    <div class="mb-2">
                        <span class="label"><i class="fas fa-users me-1"></i>Capacity:</span>
                        <span>{{ $room->capacity }} people</span>
                    </div>

                    <div class="mb-2">
                        <span class="label"><i class="fas fa-map-marker-alt me-1"></i>Location:</span>
                        <span>{{ $room->location }}</span>
                    </div>

                    @if($room->amenities)
                        <div class="mb-2">
                            <span class="label">Amenities:</span>
                            <div class="mt-1">
                                @foreach($room->amenities as $amenity)
                                    <span class="badge-amenity me-1">{{ $amenity }}</span>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Footer -->
                <div class="p-3">
                    <a href="{{ route('rooms.show', $room->id) }}" class="btn-book">
                        <i class="fas fa-calendar-plus me-2"></i>Book This Room
                    </a>
                </div>

            </div>

        </div>
    @empty
        <div class="col-12">
            <div class="empty-state">
                <i class="fas fa-info-circle me-2"></i>No rooms available at the moment.
            </div>
        </div>
    @endforelse
</div>

@endsection
