<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role; // Tambahkan ini
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        // Tetap panggil relasi role untuk menampilkan Nama Role di tabel
        $users = User::with('role')->get();
        return view('users.index', compact('users'));
    }

    public function create()
    {
        // SEKARANG WAJIB panggil Model Role agar muncul di dropdown form
        $roles = Role::all();
        return view('users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role_id'  => 'required|exists:roles,id', // Validasi agar role_id tidak NULL
        ]);

        User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role_id'  => $request->role_id, // AMBIL DARI FORM, JANGAN NULL LAGI
        ]);

        return redirect()->route('users.index')->with('success', 'User berhasil didaftarkan dengan Role yang sesuai!');
    }

    public function edit(User $user)
    {
        // Gunakan NAME (Huruf Besar) sesuai data di MySQL Anda
        if ($user->role && strtoupper($user->role->NAME) === 'SUPERADMIN') {
            abort(403, 'Gak boleh ngedit Boss Besar!');
        }
        
        $roles = Role::all(); // Tambahkan ini agar bisa ganti role saat edit
        return view('users.edit', compact('user', 'roles'));
    }

    public function destroy(User $user)
    {
        if ($user->role && strtoupper($user->role->NAME) === 'SUPERADMIN') {
            return redirect()->back()->with('error', 'Gak bisa hapus akun Superadmin, bro!');
        }

        $user->delete();
        return redirect()->route('users.index')->with('success', 'User berhasil dihapus!');
    }
}