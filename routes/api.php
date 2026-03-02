<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\RiderController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('me', [AuthController::class, 'me']);
        Route::put('device-token', [AuthController::class, 'updateDeviceToken']);
    });
});

Route::middleware('auth:sanctum')->group(function () {
    
    Route::prefix('riders')->group(function () {
        Route::get('/', [RiderController::class, 'index']);
        Route::get('/{id}', [RiderController::class, 'show']);
        
        Route::middleware('role:rider')->group(function () {
            Route::post('go-online', [RiderController::class, 'goOnline']);
            Route::post('go-offline', [RiderController::class, 'goOffline']);
            Route::put('capacity', [RiderController::class, 'updateCapacity']);
            Route::get('my-assignments', [RiderController::class, 'getMyAssignments']);
            Route::get('queue-position', [RiderController::class, 'getQueuePosition']);
            Route::get('stats', [RiderController::class, 'getStats']);
        });
        
        Route::middleware('role:admin')->group(function () {
            Route::put('/{id}', [RiderController::class, 'adminUpdateRider']);
        });
    });

    Route::prefix('bookings')->group(function () {
        Route::get('/', [BookingController::class, 'index']);
        Route::post('/', [BookingController::class, 'store'])->middleware('role:customer');
        Route::get('/{id}', [BookingController::class, 'show']);
        
        Route::post('/{id}/accept', [BookingController::class, 'acceptAssignment'])
            ->middleware('role:rider');
        Route::post('/{id}/reject', [BookingController::class, 'rejectAssignment'])
            ->middleware('role:rider');
        
        Route::post('/{id}/complete', [BookingController::class, 'complete'])
            ->middleware('role:admin');
        Route::post('/{id}/cancel', [BookingController::class, 'cancel']);
    });

    Route::get('check-timeouts', function () {
        \App\Helpers\BookingHelper::checkTimeouts();
        return response()->json(['message' => 'Timeouts checked successfully']);
    })->middleware('role:admin');
});

Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now()->toISOString(),
        'version' => '1.0.0'
    ]);
});
