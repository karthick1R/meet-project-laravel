<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'capacity',
        'amenities',
        'location',
        'is_active',
        'color',
    ];

    protected $casts = [
        'amenities' => 'array',
        'is_active' => 'boolean',
        'capacity' => 'integer',
    ];

    /**
     * Get all bookings for this room
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Get all availability blocks for this room
     */
    public function availabilities()
    {
        return $this->hasMany(RoomAvailability::class);
    }

    /**
     * Check if room is available for a given time slot
     */
    public function isAvailable($date, $startTime, $endTime, $excludeBookingId = null): bool
    {
        // Check for conflicting bookings
        $conflictingBooking = $this->bookings()
            ->where('status', '!=', 'cancelled')
            ->where('date', $date)
            ->where(function ($query) use ($startTime, $endTime) {
                $query->where(function ($q) use ($startTime, $endTime) {
                    // Booking starts before our end time and ends after our start time
                    $q->where('start_time', '<', $endTime)
                      ->where('end_time', '>', $startTime);
                });
            });

        if ($excludeBookingId) {
            $conflictingBooking->where('id', '!=', $excludeBookingId);
        }

        if ($conflictingBooking->exists()) {
            return false;
        }

        // Check for blocked/maintenance slots
        $blocked = $this->availabilities()
            ->where('date', $date)
            ->where(function ($query) use ($startTime, $endTime) {
                $query->where(function ($q) use ($startTime, $endTime) {
                    $q->where('start_time', '<', $endTime)
                      ->where('end_time', '>', $startTime);
                });
            })
            ->exists();

        return !$blocked;
    }
}
