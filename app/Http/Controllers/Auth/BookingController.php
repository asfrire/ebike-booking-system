<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\User;
use App\Events\BookingCreated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class BookingController extends Controller
{

    public function index()
    {
        $user = Auth::user();

        // fetch user bookings
        $bookings = Booking::where('user_id', $user->id)->get();
        $addressVerify = $user->address ? $user->address->verify : null;

        return Inertia::render('Bookings', [
            'bookings' => $bookings,
            'addressVerify' => $addressVerify,
        ]);
    }

    // Store new booking
    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'pax' => 'required|integer|min:1',
    //         'pickup' => 'required|string|max:255',
    //         'dropoff' => 'required|string|max:255',
    //         'pickup_time' => 'required|date',
    //     ]);

    //     $booking = Booking::create([
    //         'user_id' => Auth::id(),
    //         'rider_id' => $request->rider_id, // nullable
    //         'pax' => $request->pax,
    //         'pickup' => $request->pickup,
    //         'dropoff' => $request->dropoff,
    //         'pickup_time' => $request->pickup_time,
    //         'is_pick_up' => false,
    //     ]);

    //     return response()->json([
    //         'message' => 'Booking created successfully!',
    //         'booking' => $booking,
    //     ]);

    //     // return redirect()->route('bookings.index')->with('success', 'Booking created successfully.');
    // }

    // BookingController@store
    public function store(Request $request)
    {
        $request->validate([
            'pax' => 'required|integer|min:1',
            'pickup' => 'required|string|max:255',
            'dropoff' => 'required|string|max:255',
            'pickup_time' => 'required|string|max:255',
        ]);

        $booking = Booking::create([
            'user_id' => Auth::id(),
            'rider_id' => $request->rider_id,
            'pax' => $request->pax,
            'pickup' => $request->pickup,
            'dropoff' => $request->dropoff,
            'pickup_time' => $request->pickup_time,
            'is_pick_up' => false,
        ]);

        // 🔴 Broadcast event to Reverb
        // broadcast(new \App\Events\BookingCreated($booking))->toOthers();
        // broadcast(new BookingCreated($booking))->toOthers();
        BookingCreated::dispatch($booking);

        return redirect()->route('bookings.index')->with('success', 'Booking created successfully.');
    }


}
