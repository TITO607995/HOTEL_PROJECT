<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($request->only('email', 'password'))) {
            $user = User::where('email', $request->email)->firstOrFail();
            
            // Bikin Token khusus untuk Flutter
            $token = $user->createToken('auth_token_flutter')->plainTextToken;

            // Kembalikan response berupa JSON (Bukan View/HTML)
            return response()->json([
                'status' => 'success',
                'message' => 'Login Berhasil',
                'data' => $user,
                'access_token' => $token,
                'token_type' => 'Bearer'
            ], 200);
        }

        return response()->json([
            'status' => 'failed',
            'message' => 'Email atau Password salah'
        ], 401);
    }
}