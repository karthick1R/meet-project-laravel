<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomAvailability extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_id',
        'date',
        'start_time',
        'end_time',
        'type',
        'reason',
    ];

    protected $casts = [
        'date' => 'date',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
    ];

    /**
     * Get the room for this availability block
     */
    public function room()
    {
        return $this->belongsTo(Room::class);
    }
}
