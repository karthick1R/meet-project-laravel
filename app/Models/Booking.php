<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_id',
        'user_id',
        'title',
        'description',
        'date',
        'start_time',
        'end_time',
        'recurrence',
        'recurrence_end_date',
        'status',
    ];

    protected $casts = [
        'date' => 'date',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'recurrence_end_date' => 'date',
    ];

    /**
     * Get the room for this booking
     */
    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    /**
     * Get the organizer (user) for this booking
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all invitations for this booking
     */
    public function invitations()
    {
        return $this->hasMany(Invitation::class);
    }

    /**
     * Check if booking is cancelled
     */
    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    /**
     * Get formatted start datetime
     */
    public function getStartDateTimeAttribute()
    {
        return Carbon::parse($this->date)->setTimeFromTimeString($this->start_time);
    }

    /**
     * Get formatted end datetime
     */
    public function getEndDateTimeAttribute()
    {
        return Carbon::parse($this->date)->setTimeFromTimeString($this->end_time);
    }
}
