<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        // 1. Validasi Input Dasar
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // 2. Buat Unique Key berdasarkan IP User
        $throttleKey = 'login:' . $request->ip();

        // 3. CEK LIMITER (Inti Keamanan)
        // Jika sudah lebih dari 5 kali percobaan gagal
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            
            // Lempar error balik ke halaman login (akan ditangkap SweetAlert)
            throw ValidationException::withMessages([
                'email' => "KEAMANAN TERPICU: Terlalu banyak percobaan. Sistem membeku selama $seconds detik!",
            ]);
        }

        // 4. PROSES AUTHENTICATION
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            // Jika Berhasil:
            RateLimiter::clear($throttleKey); // Hapus jejak limit
            $request->session()->regenerate(); // Hindari session fixation
            
            return redirect()->intended('dashboard'); // Arahkan ke dashboard
        }

        // 5. JIKA GAGAL LOGIN
        // Tambah hitungan percobaan gagal
        RateLimiter::hit($throttleKey, 60); // Penalti 60 detik

        return back()->withErrors([
            'email' => 'Kredensial tidak cocok dengan data kami.',
        ])->withInput($request->only('email'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}