<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingRider extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'rider_id',
        'allocated_seats',
        'status',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function rider()
    {
        return $this->belongsTo(Rider::class);
    }

    public function isExpired(): bool
    {
        return $this->status === 'assigned' && $this->expires_at->isPast();
    }

    public function canAccept(): bool
    {
        return $this->status === 'assigned' && $this->expires_at->isFuture();
    }

    public function scopeAssigned($query)
    {
        return $query->where('status', 'assigned');
    }

    public function scopeExpired($query)
    {
        return $query->where('status', 'assigned')
                    ->where('expires_at', '<', now());
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', ['assigned', 'accepted']);
    }
}
