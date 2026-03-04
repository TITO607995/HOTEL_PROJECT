<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        // 1. PROTEKSI HONEYPOT (Anti-Bot)
        // Pastikan nama field ini sama dengan yang ada di <input> hidden Blade kamu
        if ($request->filled('important_field_check') || $request->filled('my_security_field')) {
            return response()->json(['message' => 'Bot detected'], 422);
        }

        // 2. VALIDASI INPUT
        $request->validate([
            'email' => 'required|email|string',
            'password' => 'required|string',
        ]);

        // 3. RATE LIMITING (Anti-Brute Force)
        $throttleKey = Str::lower($request->input('email')) . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            
            // Tambahan: Simpan ke session agar Blade bisa baca dengan mudah
            session(['login_lock_seconds' => $seconds]);

            throw ValidationException::withMessages([
                'email' => "KEAMANAN TERPICU: Terlalu banyak percobaan. Sistem membeku selama $seconds detik!",
            ]);
        }

        // 4. PROSES AUTHENTICATION
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            RateLimiter::clear($throttleKey); 
            session()->forget('login_lock_seconds'); // Hapus session lock jika berhasil
            $request->session()->regenerate(); 
            
            return redirect()->intended('dashboard');
        }

        // 5. JIKA GAGAL LOGIN
        RateLimiter::hit($throttleKey, 60); 
        
        // Update session sisa waktu setiap gagal
        $seconds = RateLimiter::availableIn($throttleKey);
        if ($seconds > 0) {
            session(['login_lock_seconds' => $seconds]);
        }

        return back()->withErrors([
            'email' => 'Email atau password tidak terdaftar di sistem kami.',
        ])->withInput($request->except('password'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}