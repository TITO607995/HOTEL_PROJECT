<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\BlacklistIp;
use Illuminate\Http\Request;

class BlockIpMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Cek apakah IP pengunjung ada di database blacklist
        $isBlocked = BlacklistIp::where('ip_address', $request->ip())->exists();

        if ($isBlocked) {
            abort(403, 'Akses Anda diblokir oleh sistem keamanan Hotel SIG. Jika Anda merasa ini adalah kesalahan, silakan hubungi administrator. Terima kasih.');
        }

        return $next($request);
    }
}