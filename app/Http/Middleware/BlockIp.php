<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BlockIp
{
    public function handle(Request $request, Closure $next): Response
    {
        // GANTI IP HP DI SINI
        $blockedIps = [
            '192.168.1.7', // contoh IP HP
        ];

        if (in_array($request->ip(), $blockedIps)) {
            return response()->json([
                'status' => 'blocked',
                'ip' => $request->ip(),
                'message' => 'Your IP is blocked'
            ], 403);
        }

        return $next($request);
    }
}