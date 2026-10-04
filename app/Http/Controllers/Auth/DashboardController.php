<?php

// namespace App\Http\Controllers\Auth;

// use App\Http\Controllers\Controller;
// use Illuminate\Support\Facades\Auth;
// use App\Models\Booking;
// use Inertia\Inertia;

// class DashboardController extends Controller
// {
//     public function index()
//     {
//         $user = Auth::user();

//         // Get address verify status
//         $addressVerify = $user->address ? $user->address->verify : null;

//         // Get user’s bookings
//         $bookings = Booking::where('user_id', $user->id)
//             ->latest()
//             ->take(5) // or remove ->take(5) to show all
//             ->get();

//         return Inertia::render('Dashboard', [
//             'addressVerify' => $addressVerify,
//             'bookings' => $bookings,
//         ]);
//     }
// }


namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\{Auth, Log};
use App\Models\Booking;
use Inertia\Inertia;
use Illuminate\Support\Facades\Broadcast;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Get address verify status
        $addressVerify = $user->address ? $user->address->verify : null;

        // Get user’s bookings
        $bookings = Booking::where('user_id', $user->id)
            ->latest()
            ->take(5) // remove take(5) if you want all
            ->get();

         // Reverb config
        $reverb = [
            'key'    => config('broadcasting.connections.reverb.key'),
            'host'   => config('broadcasting.connections.reverb.options.host') ?? '127.0.0.1',
            'port'   => config('broadcasting.connections.reverb.options.port') ?? 8080,
            'scheme' => config('broadcasting.connections.reverb.options.scheme') ?? 'http',
        ];

        // 👀 Debug log
        Log::info('Dashboard Reverb config:', $reverb);

        return Inertia::render('Dashboard', [
            'addressVerify' => $addressVerify,
            'bookings'      => $bookings,
            'reverb'        => $reverb,
        ]);
    }
}
