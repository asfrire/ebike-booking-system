<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Rider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RiderController extends Controller
{
    // Get rider info for logged-in user
    public function index()
    {
        $rider = Rider::where('user_id', Auth::id())->first();
        return response()->json($rider);
    }

    // Register or update rider
    public function store(Request $request)
    {
        $request->validate([
            'available_seats' => 'required|integer|min:1',
        ]);

        $rider = Rider::updateOrCreate(
            ['user_id' => $request->user_id],
            ['available_seats' => $request->available_seats]
        );

        return response()->json([
            'message' => 'Rider info saved successfully!',
            'rider' => $rider,
        ]);
    }
}
