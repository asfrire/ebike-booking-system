<?php

namespace App\Http\Controllers;

use App\Helpers\BookingHelper;
use App\Models\Rider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class RiderController extends controller
{
    public function index(Request $request)
    {
        $riders = Rider::with(['user', 'bookingRiders.booking'])
            ->when($request->online_only, function ($query) {
                return $query->where('is_online', true);
            })
            ->when($request->available_only, function ($query) {
                return $query->whereDoesntHave('bookingRiders', function ($query) {
                    $query->whereIn('status', ['assigned', 'accepted']);
                });
            })
            ->orderBy('queue_position', 'asc')
            ->paginate(20);

        return response()->json(['riders' => $riders]);
    }

    public function show(Request $request, $id)
    {
        $rider = Rider::with(['user', 'bookingRiders.booking.customer'])
            ->findOrFail($id);

        return response()->json(['rider' => $rider]);
    }

    public function goOnline(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'capacity' => 'nullable|integer|min:2|max:5',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        return DB::transaction(function () use ($request) {
            $rider = $request->user()->rider;

            if ($rider->is_online) {
                return response()->json(['error' => 'Rider is already online'], 400);
            }

            $maxPosition = Rider::online()->max('queue_position') ?? 0;
            
            $rider->update([
                'is_online' => true,
                'queue_position' => $maxPosition + 1,
                'capacity' => $request->capacity ?? $rider->capacity,
            ]);

            $rider->load('user');

            return response()->json([
                'message' => 'Rider is now online',
                'rider' => $rider
            ]);
        });
    }

    public function goOffline(Request $request)
    {
        return DB::transaction(function () use ($request) {
            $rider = $request->user()->rider;

            if (!$rider->is_online) {
                return response()->json(['error' => 'Rider is already offline'], 400);
            }

            $hasActiveAssignments = $rider->bookingRiders()
                ->whereIn('status', ['assigned', 'accepted'])
                ->exists();

            if ($hasActiveAssignments) {
                return response()->json(['error' => 'Cannot go offline with active assignments'], 400);
            }

            $rider->update([
                'is_online' => false,
                'queue_position' => null,
            ]);

            BookingHelper::reorderRiderQueue();

            $rider->load('user');

            return response()->json([
                'message' => 'Rider is now offline',
                'rider' => $rider
            ]);
        });
    }

    public function updateCapacity(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'capacity' => 'required|integer|min:2|max:5',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $rider = $request->user()->rider;

        $hasActiveAssignments = $rider->bookingRiders()
            ->whereIn('status', ['assigned', 'accepted'])
            ->exists();

        if ($hasActiveAssignments) {
            return response()->json(['error' => 'Cannot update capacity with active assignments'], 400);
        }

        $rider->update(['capacity' => $request->capacity]);

        return response()->json([
            'message' => 'Capacity updated successfully',
            'rider' => $rider->load('user')
        ]);
    }

    public function getMyAssignments(Request $request)
    {
        BookingHelper::checkTimeouts();

        $rider = $request->user()->rider;
        
        $assignments = $rider->bookingRiders()
            ->with(['booking.customer', 'booking.bookingRiders.rider.user'])
            ->whereIn('status', ['assigned', 'accepted'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json(['assignments' => $assignments]);
    }

    public function getQueuePosition(Request $request)
    {
        $rider = $request->user()->rider;

        if (!$rider->is_online) {
            return response()->json([
                'is_online' => false,
                'queue_position' => null,
                'riders_ahead' => 0
            ]);
        }

        $ridersAhead = Rider::online()
            ->where('queue_position', '<', $rider->queue_position)
            ->count();

        return response()->json([
            'is_online' => $rider->is_online,
            'queue_position' => $rider->queue_position,
            'riders_ahead' => $ridersAhead,
            'capacity' => $rider->capacity
        ]);
    }

    public function getStats(Request $request)
    {
        $rider = $request->user()->rider;

        $today = now()->startOfDay();
        $thisMonth = now()->startOfMonth();

        $stats = [
            'total_completed' => $rider->bookingRiders()
                ->where('status', 'accepted')
                ->whereHas('booking', function ($query) {
                    $query->where('status', 'completed');
                })
                ->count(),
            'today_completed' => $rider->bookingRiders()
                ->where('status', 'accepted')
                ->whereHas('booking', function ($query) use ($today) {
                    $query->where('status', 'completed')
                          ->where('created_at', '>=', $today);
                })
                ->count(),
            'this_month_completed' => $rider->bookingRiders()
                ->where('status', 'accepted')
                ->whereHas('booking', function ($query) use ($thisMonth) {
                    $query->where('status', 'completed')
                          ->where('created_at', '>=', $thisMonth);
                })
                ->count(),
            'total_passengers' => $rider->bookingRiders()
                ->where('status', 'accepted')
                ->whereHas('booking', function ($query) {
                    $query->where('status', 'completed');
                })
                ->sum('allocated_seats'),
            'acceptance_rate' => $this->calculateAcceptanceRate($rider),
        ];

        return response()->json(['stats' => $stats]);
    }

    private function calculateAcceptanceRate(Rider $rider)
    {
        $totalAssignments = $rider->bookingRiders()->count();
        
        if ($totalAssignments === 0) {
            return 0;
        }

        $acceptedAssignments = $rider->bookingRiders()
            ->where('status', 'accepted')
            ->count();

        return round(($acceptedAssignments / $totalAssignments) * 100, 2);
    }

    public function adminUpdateRider(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'is_online' => 'boolean',
            'queue_position' => 'nullable|integer|min:1',
            'capacity' => 'nullable|integer|min:2|max:5',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $rider = Rider::findOrFail($id);

        $updateData = [];
        
        if ($request->has('is_online')) {
            $updateData['is_online'] = $request->is_online;
            
            if (!$request->is_online) {
                $updateData['queue_position'] = null;
            }
        }

        if ($request->has('capacity')) {
            $updateData['capacity'] = $request->capacity;
        }

        if ($request->has('queue_position') && $rider->is_online) {
            $updateData['queue_position'] = $request->queue_position;
        }

        if (empty($updateData)) {
            return response()->json(['error' => 'No valid fields to update'], 400);
        }

        return DB::transaction(function () use ($rider, $updateData) {
            $rider->update($updateData);

            if (isset($updateData['is_online']) || isset($updateData['queue_position'])) {
                BookingHelper::reorderRiderQueue();
            }

            $rider->load('user');

            return response()->json([
                'message' => 'Rider updated successfully',
                'rider' => $rider
            ]);
        });
    }
}
