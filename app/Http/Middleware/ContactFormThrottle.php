<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

class ContactFormThrottle
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
{
    // The user is identified by their IP Address
    $ip = $request->ip();

    // 1. Short-term limit: 1 submission per 5 minutes

    $shortTermKey = 'contact_short_' . $ip;

    // Check if the submission limit is exceeded (more than 1 attempt in the set time)
    if (RateLimiter::tooManyAttempts($shortTermKey, 1)) {
        $seconds = RateLimiter::availableIn($shortTermKey);

        return redirect()->back()->with('error', 'Please wait. You can submit the form only once every 5 minutes. Please try again after ' . $seconds . ' seconds.');
    }
    // Record the hit and set the expiration time for 5 minutes (60 seconds * 5 minutes)
    RateLimiter::hit($shortTermKey, 60 * 5); // 5 minutes = 300 seconds

    // 2. Long-term limit: 10 submissions per 24 hours

    $longTermKey = 'contact_long_' . $ip;

    // Check if the submission limit is exceeded (more than 10 attempts in 24 hours)
    if (RateLimiter::tooManyAttempts($longTermKey, 10)) {
        $seconds = RateLimiter::availableIn($longTermKey);

        return redirect()->back()->with('error', 'Sorry, you have exceeded the 24-hour submission limit of 10 forms. Please try again after ' . ceil($seconds / 3600) . ' hours.');
    }
    // Record the hit and set the expiration time for 24 hours (60 seconds * 60 minutes * 24 hours)
    RateLimiter::hit($longTermKey, 60 * 60 * 24); // 24 hours = 86,400 seconds

    // If all checks pass, proceed with the request
    return $next($request);
}
}
