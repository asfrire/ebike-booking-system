<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rider extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'is_online',
        'queue_position',
        'capacity',
    ];

    protected $casts = [
        'is_online' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function bookingRiders()
    {
        return $this->hasMany(BookingRider::class);
    }

    public function activeBookings()
    {
        return $this->bookingRiders()
            ->where('status', 'accepted')
            ->whereHas('booking', function ($query) {
                $query->whereIn('status', ['accepted', 'partially_assigned']);
            });
    }

    public function isAvailable(): bool
    {
        return $this->is_online && $this->activeBookings()->count() === 0;
    }

    public function scopeOnline($query)
    {
        return $query->where('is_online', true);
    }

    public function scopeAvailable($query)
    {
        return $query->online()->whereDoesntHave('bookingRiders', function ($query) {
            $query->whereIn('status', ['assigned', 'accepted']);
        });
    }

    public function scopeByQueuePosition($query)
    {
        return $query->orderBy('queue_position', 'asc');
    }
}
