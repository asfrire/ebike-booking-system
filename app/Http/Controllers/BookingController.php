<?php

namespace App\Http\Controllers;

use App\Helpers\BookingHelper;
use App\Models\Booking;
use App\Models\BookingRider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        BookingHelper::checkTimeouts();

        $user = $request->user();
        
        if ($user->isCustomer()) {
            $bookings = Booking::where('customer_id', $user->id)
                ->with(['bookingRiders.rider.user', 'customer'])
                ->orderBy('created_at', 'desc')
                ->paginate(20);
        } elseif ($user->isRider()) {
            $bookingIds = $user->rider->bookingRiders()
                ->pluck('booking_id')
                ->unique();
            
            $bookings = Booking::whereIn('id', $bookingIds)
                ->with(['bookingRiders.rider.user', 'customer'])
                ->orderBy('created_at', 'desc')
                ->paginate(20);
        } else {
            $bookings = Booking::with(['bookingRiders.rider.user', 'customer'])
                ->orderBy('created_at', 'desc')
                ->paginate(20);
        }

        return response()->json(['bookings' => $bookings]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'pickup_location' => 'required|string|max:255',
            'dropoff_location' => 'required|string|max:255',
            'pax' => 'required|integer|min:1|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        return DB::transaction(function () use ($request) {
            $booking = Booking::create([
                'customer_id' => $request->user()->id,
                'pickup_location' => $request->pickup_location,
                'dropoff_location' => $request->dropoff_location,
                'pax' => $request->pax,
                'remaining_pax' => $request->pax,
                'status' => 'pending',
            ]);

            BookingHelper::assignRiders($booking);

            $booking->load(['bookingRiders.rider.user', 'customer']);

            return response()->json(['booking' => $booking], 201);
        });
    }

    public function show(Request $request, $id)
    {
        BookingHelper::checkTimeouts();

        $user = $request->user();
        $booking = Booking::with(['bookingRiders.rider.user', 'customer'])
            ->findOrFail($id);

        if ($user->isCustomer() && $booking->customer_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if ($user->isRider()) {
            $hasAccess = $booking->bookingRiders()
                ->where('rider_id', $user->rider->id)
                ->exists();
            
            if (!$hasAccess) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
        }

        return response()->json(['booking' => $booking]);
    }

    public function acceptAssignment(Request $request, $bookingId)
    {
        $validator = Validator::make($request->all(), []);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        return DB::transaction(function () use ($request, $bookingId) {
            $rider = $request->user()->rider;
            
            $booking = Booking::lockForUpdate()->findOrFail($bookingId);
            
            if (!$booking->canBeAccepted()) {
                return response()->json(['error' => 'Booking cannot be accepted'], 400);
            }

            $assignment = BookingRider::where('booking_id', $bookingId)
                ->where('rider_id', $rider->id)
                ->where('status', 'assigned')
                ->lockForUpdate()
                ->first();

            if (!$assignment) {
                return response()->json(['error' => 'No valid assignment found'], 404);
            }

            if ($assignment->expires_at->isPast()) {
                $isReassigned = BookingRider::where('booking_id', $bookingId)
                    ->where('rider_id', '!=', $rider->id)
                    ->where('status', 'accepted')
                    ->exists();

                if ($isReassigned) {
                    return response()->json(['error' => 'Assignment expired and seats were reassigned'], 410);
                }

                $totalAccepted = BookingRider::where('booking_id', $bookingId)
                    ->where('status', 'accepted')
                    ->sum('allocated_seats');

                $totalAssigned = BookingRider::where('booking_id', $bookingId)
                    ->whereIn('status', ['assigned', 'accepted'])
                    ->sum('allocated_seats');

                if ($totalAccepted + $assignment->allocated_seats > $booking->pax) {
                    return response()->json(['error' => 'Seats already reassigned to other riders'], 409);
                }
            }

            $assignment->status = 'accepted';
            $assignment->save();

            $allAccepted = BookingRider::where('booking_id', $bookingId)
                ->where('status', 'assigned')
                ->count() === 0;

            $totalAcceptedSeats = BookingRider::where('booking_id', $bookingId)
                ->where('status', 'accepted')
                ->sum('allocated_seats');

            if ($totalAcceptedSeats >= $booking->pax || $allAccepted) {
                $booking->status = 'accepted';
                $booking->save();
            }

            $booking->load(['bookingRiders.rider.user', 'customer']);

            return response()->json([
                'message' => 'Assignment accepted successfully',
                'booking' => $booking
            ]);
        });
    }

    public function rejectAssignment(Request $request, $bookingId)
    {
        return DB::transaction(function () use ($request, $bookingId) {
            $rider = $request->user()->rider;
            
            $assignment = BookingRider::where('booking_id', $bookingId)
                ->where('rider_id', $rider->id)
                ->where('status', 'assigned')
                ->firstOrFail();

            $assignment->status = 'rejected';
            $assignment->save();

            $booking = $assignment->booking;
            $booking->remaining_pax += $assignment->allocated_seats;
            
            if ($booking->remaining_pax === $booking->pax) {
                $booking->status = 'pending';
            } else {
                $booking->status = 'partially_assigned';
            }
            
            $booking->save();

            BookingHelper::assignRiders($booking);

            $booking->load(['bookingRiders.rider.user', 'customer']);

            return response()->json([
                'message' => 'Assignment rejected successfully',
                'booking' => $booking
            ]);
        });
    }

    public function complete(Request $request, $id)
    {
        $validator = Validator::make($request->all(), []);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $booking = Booking::findOrFail($id);

        if (!in_array($booking->status, ['accepted', 'partially_assigned'])) {
            return response()->json(['error' => 'Booking cannot be completed'], 400);
        }

        $booking->status = 'completed';
        $booking->save();

        return response()->json([
            'message' => 'Booking completed successfully',
            'booking' => $booking
        ]);
    }

    public function cancel(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'reason' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = $request->user();
        $booking = Booking::findOrFail($id);

        if ($user->isCustomer() && $booking->customer_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if (!in_array($booking->status, ['pending', 'partially_assigned', 'fully_assigned'])) {
            return response()->json(['error' => 'Booking cannot be cancelled'], 400);
        }

        $booking->status = 'cancelled';
        $booking->save();

        BookingRider::where('booking_id', $booking->id)
            ->whereIn('status', ['assigned', 'accepted'])
            ->update(['status' => 'rejected']);

        return response()->json([
            'message' => 'Booking cancelled successfully',
            'booking' => $booking
        ]);
    }
}
