<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LogApiRequests
{
    public function handle(Request $request, Closure $next)
    {
        Log::info('API Request', [
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'params' => $request->all(),
            'headers' => $request->headers->all()
        ]);

        // Handle the request and capture the response
        $response = $next($request);

        // Log the response after the request has been handled
        Log::info('API Response', [
            'status' => $response->status(),
            'body' => $response->getContent(),
            'headers' => $response->headers->all(),
        ]);

        return $response;

        //return $next($request);
    }
}
