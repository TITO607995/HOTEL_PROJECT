<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Validation\ValidationException;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

public function boot(): void
{
    Paginator::useTailwind();

    RateLimiter::for('login', function (Request $request) {
        // Key ini harus konsisten digunakan
        $throttleKey = 'login:' . $request->ip();

        return Limit::perMinute(5)->by($request->ip())->response(function (Request $request, array $headers) use ($throttleKey) {
            
            $seconds = RateLimiter::availableIn($throttleKey);
            
            // Backup ke session untuk Alpine.js
            session()->put('is_limited', true);
            session()->put('seconds_left', $seconds);

            // Lempar error 429 agar ditangkap SweetAlert
            throw ValidationException::withMessages([
                'email' => ["Keamanan sistem: Terlalu banyak percobaan. Tunggu $seconds detik."],
            ])->status(429);
        });
    });
}
}