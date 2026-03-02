<?php

namespace App\Helpers;

use App\Models\Booking;
use App\Models\BookingRider;
use App\Models\Rider;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BookingHelper
{
    public static function checkTimeouts()
    {
        $expiredAssignments = BookingRider::expired()->get();

        foreach ($expiredAssignments as $assignment) {
            DB::transaction(function () use ($assignment) {
                $booking = $assignment->booking;
                $rider = $assignment->rider;

                $assignment->status = 'expired';
                $assignment->save();

                $booking->remaining_pax += $assignment->allocated_seats;
                
                if ($booking->remaining_pax === $booking->pax) {
                    $booking->status = 'pending';
                } else {
                    $booking->status = 'partially_assigned';
                }
                
                $booking->save();

                self::moveRiderToEndOfQueue($rider);

                Log::info("Assignment expired for booking {$booking->id}, rider {$rider->id}");
            });
        }

        if ($expiredAssignments->isNotEmpty()) {
            foreach ($expiredAssignments as $assignment) {
                self::assignRiders($assignment->booking);
            }
        }

        return $expiredAssignments->count();
    }

    public static function assignRiders(Booking $booking)
    {
        $remaining = $booking->remaining_pax;

        if ($remaining <= 0) {
            return;
        }

        $availableRiders = Rider::available()
            ->byQueuePosition()
            ->get();

        foreach ($availableRiders as $rider) {
            if ($remaining <= 0) {
                break;
            }

            $seats = min($rider->capacity, $remaining);

            BookingRider::create([
                'booking_id' => $booking->id,
                'rider_id' => $rider->id,
                'allocated_seats' => $seats,
                'status' => 'assigned',
                'expires_at' => now()->addMinutes(3),
            ]);

            $remaining -= $seats;

            self::sendPushNotification($rider, $booking);
        }

        $booking->remaining_pax = $remaining;
        
        if ($remaining > 0) {
            $booking->status = 'partially_assigned';
        } else {
            $booking->status = 'fully_assigned';
        }
        
        $booking->save();

        Log::info("Assigned riders to booking {$booking->id}, remaining pax: {$remaining}");
    }

    public static function moveRiderToEndOfQueue(Rider $rider)
    {
        if (!$rider->is_online) {
            $rider->queue_position = null;
            $rider->save();
            return;
        }

        $maxPosition = Rider::online()->max('queue_position') ?? 0;
        $rider->queue_position = $maxPosition + 1;
        $rider->save();
    }

    public static function reorderRiderQueue()
    {
        $onlineRiders = Rider::online()
            ->orderBy('queue_position', 'asc')
            ->get();

        $position = 1;
        foreach ($onlineRiders as $rider) {
            $rider->queue_position = $position;
            $rider->save();
            $position++;
        }
    }

    private static function sendPushNotification(Rider $rider, Booking $booking)
    {
        if (!$rider->user->device_token) {
            return;
        }

        $title = 'New Booking Assignment';
        $body = "You have been assigned {$booking->pax} passengers from {$booking->pickup_location} to {$booking->dropoff_location}";
        $data = [
            'booking_id' => $booking->id,
            'type' => 'new_assignment',
            'expires_at' => now()->addMinutes(3)->toISOString(),
        ];

        try {
            $fcmUrl = 'https://fcm.googleapis.com/fcm/send';
            $serverKey = config('services.fcm.server_key');

            $payload = [
                'to' => $rider->user->device_token,
                'notification' => [
                    'title' => $title,
                    'body' => $body,
                    'sound' => 'default',
                ],
                'data' => $data,
                'priority' => 'high',
                'time_to_live' => 180,
            ];

            $headers = [
                'Authorization: key=' . $serverKey,
                'Content-Type: application/json',
            ];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $fcmUrl);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
            
            $result = curl_exec($ch);
            curl_close($ch);

            Log::info("Push notification sent to rider {$rider->id}", ['result' => $result]);
        } catch (\Exception $e) {
            Log::error("Failed to send push notification to rider {$rider->id}: " . $e->getMessage());
        }
    }
}
