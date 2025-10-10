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
    public function handle(Request $request, Closure $next)
    {
        $ip = $request->ip();
        $shortTermKey = 'contact_short_' . $ip;
        $longTermKey = 'contact_long_' . $ip;

        // Short-term check (5 min)
        if (RateLimiter::tooManyAttempts($shortTermKey, 1)) {
            $seconds = RateLimiter::availableIn($shortTermKey);
            return redirect()->back()->with('error', "Please wait. You can submit once every 5 minutes. Try again after {$seconds} seconds.");
        }

        // Long-term check (24h)
        if (RateLimiter::tooManyAttempts($longTermKey, 10)) {
            $seconds = RateLimiter::availableIn($longTermKey);
            return redirect()->back()->with('error', "You have reached 10 submissions in 24 hours. Try again after " . ceil($seconds / 3600) . " hours.");
        }

        return $next($request);
    }
}
