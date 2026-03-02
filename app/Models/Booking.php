<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'pickup_location',
        'dropoff_location',
        'pax',
        'remaining_pax',
        'status',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function bookingRiders()
    {
        return $this->hasMany(BookingRider::class);
    }

    public function assignedRiders()
    {
        return $this->bookingRiders()->where('status', 'assigned');
    }

    public function acceptedRiders()
    {
        return $this->bookingRiders()->where('status', 'accepted');
    }

    public function expiredRiders()
    {
        return $this->bookingRiders()->where('status', 'expired');
    }

    public function riders()
    {
        return $this->belongsToMany(Rider::class, 'booking_riders')
            ->withPivot(['allocated_seats', 'status', 'expires_at', 'created_at', 'updated_at']);
    }

    public function scopePending($query)
    {
        return $query->whereIn('status', ['pending', 'partially_assigned', 'fully_assigned']);
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', ['pending', 'partially_assigned', 'fully_assigned', 'accepted']);
    }

    public function isFullyAssigned(): bool
    {
        return $this->status === 'fully_assigned' || $this->remaining_pax === 0;
    }

    public function isPartiallyAssigned(): bool
    {
        return $this->status === 'partially_assigned' && $this->remaining_pax > 0;
    }

    public function canBeAccepted(): bool
    {
        return in_array($this->status, ['pending', 'partially_assigned', 'fully_assigned']);
    }

    public function getTotalAcceptedSeats(): int
    {
        return $this->acceptedRiders()->sum('allocated_seats');
    }

    public function getTotalAssignedSeats(): int
    {
        return $this->bookingRiders()->whereIn('status', ['assigned', 'accepted'])->sum('allocated_seats');
    }
}
