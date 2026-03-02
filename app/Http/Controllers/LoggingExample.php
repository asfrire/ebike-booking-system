<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LoggingExample extends Controller
{
    public function testLogging()
    {
        // Debug logging
        Log::debug('This is a debug message');
        
        // Info logging
        Log::info('User action performed', ['user_id' => auth()->id()]);
        
        // Warning logging
        Log::warning('Something unusual happened');
        
        // Error logging
        Log::error('An error occurred', ['exception' => 'Sample error']);
        
        return response()->json(['message' => 'Logs written']);
    }
}
