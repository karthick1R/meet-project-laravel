<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invitation extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'email',
        'status',
        'responded_at',
    ];

    protected $casts = [
        'responded_at' => 'datetime',
    ];

    /**
     * Get the booking for this invitation
     */
    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}
