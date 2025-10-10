<?php
namespace App\Services;

use Illuminate\Support\Facades\RateLimiter;

class ContactRateLimitService
{
    public static function hitShortTerm($ip)
    {
        $key = 'contact_short_' . $ip;
        RateLimiter::hit($key, 60 * .3);
    }

    public static function hitLongTerm($ip)
    {
        $key = 'contact_long_' . $ip;
        RateLimiter::hit($key, 60 * 60 * 24);
    }

    public static function clearShortTerm($ip)
    {
        RateLimiter::clear('contact_short_' . $ip);
    }

    public static function clearLongTerm($ip)
    {
        RateLimiter::clear('contact_long_' . $ip);
    }
}

?>
