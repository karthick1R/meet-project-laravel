<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ProductKey extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
        'phone',
        'product_key',
        'is_active',
        'user_id',
        'registration_token',
        'used_at',
        'razorpay_order_id',
        'razorpay_payment_id',
        'payment_status',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'used_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function generateKey(): string
    {
        do {
            $key = strtoupper(
                implode('-', [
                    Str::random(4),
                    Str::random(4),
                    Str::random(4),
                    Str::random(4),
                ])
            );
        } while (self::where('product_key', $key)->exists());

        return $key;
    }

    public static function generateRegistrationToken(): string
    {
        return Str::random(64);
    }

    public function isUsed(): bool
    {
        return $this->used_at !== null;
    }

    public function markAsUsed(): void
    {
        $this->update(['used_at' => now()]);
    }
}

