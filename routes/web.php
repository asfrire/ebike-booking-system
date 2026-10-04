<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\BookingController;
use App\Http\Controllers\Auth\RiderController;
use App\Http\Controllers\Auth\DashboardController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Illuminate\Support\Facades\{Auth, Log};


// Route::get('/', function () {
//     return Inertia::render('login', [
//         'canLogin' => Route::has('login'),
//         'canRegister' => Route::has('register'),
//         'laravelVersion' => Application::VERSION,
//         'phpVersion' => PHP_VERSION,
//     ]);
// });

Route::get('/', [AuthenticatedSessionController::class, 'create'])
        ->name('login');



// Route::get('/dashboard', function () {
//     $user = Auth::user();

//     $addressVerify = $user->address ? $user->address->verify : null;

//     return Inertia::render('Dashboard', [
//         'addressVerify' => $addressVerify,
//     ]);
// })->middleware(['auth', 'verified'])->name('dashboard');


Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');



Route::middleware('auth')->group(function () {

    // Profile Routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

     // Booking routes
    Route::get('/bookings', [BookingController::class, 'index'])->name('bookings.index');   // list bookings
    Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store');  // create booking
    Route::get('/bookings/{id}', [BookingController::class, 'show'])->name('bookings.show'); // single booking

    // Route::put('/bookings/{id}', [BookingController::class, 'update']); // update booking
    // Route::delete('/bookings/{id}', [BookingController::class, 'destroy']); // delete booking

    // Rider routes
    Route::get('/rider', [RiderController::class, 'index']);   // current rider info
    Route::post('/rider', [RiderController::class, 'store']);  // create or update rider
});

require __DIR__.'/auth.php';
