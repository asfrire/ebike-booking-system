<?php

// namespace App\Events;

// use App\Models\Booking;
// use Illuminate\Broadcasting\Channel;
// use Illuminate\Broadcasting\InteractsWithSockets;
// use Illuminate\Broadcasting\PresenceChannel;
// use Illuminate\Broadcasting\PrivateChannel;
// use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
// use Illuminate\Foundation\Events\Dispatchable;
// use Illuminate\Queue\SerializesModels;

// class BookingCreated implements ShouldBroadcast
// {
//     use Dispatchable, InteractsWithSockets, SerializesModels;

//     public $booking;

//     public function __construct(Booking $booking)
//     {
//         $this->booking = $booking;
//     }

//     public function broadcastOn(): array
//     {
//         return [
//             new PrivateChannel('bookings.' . $this->booking->user_id), // user-specific
//             new Channel('bookings'), // public channel if needed
//         ];
//     }

//     public function broadcastAs()
//     {
//         return 'BookingCreated';
//     }
    
// }


namespace App\Events;

use App\Models\Booking;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BookingCreated implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;

    public $booking;

    public function __construct(Booking $booking)
    {
        $this->booking = $booking;
    }

    public function broadcastOn()
    {
        return new Channel('bookings'); // 👈 public channel
    }

    public function broadcastAs()
    {
        return 'BookingCreated'; // 👈 event name
    }

    public function broadcastWith()
    {
        return [
            'id' => $this->booking->id,
            'pax' => $this->booking->pax,
            'pickup' => $this->booking->pickup,
            'dropoff' => $this->booking->dropoff,
            'pickup_time' => $this->booking->pickup_time,
        ];
    }
}
