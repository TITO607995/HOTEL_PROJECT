<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        // Tetap panggil relasi role cuma buat nampilin status di tabel
        $users = User::with('role')->get();
        return view('users.index', compact('users'));
    }

    public function create()
    {
        // Gak perlu panggil Model Role lagi di sini
        return view('users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => null, // Default kosong, nanti diisi di menu Assign Role
        ]);

        return redirect()->route('users.index')->with('success', 'User berhasil didaftarkan! Jangan lupa tentukan rolenya di menu Assign Role.');
    }

    public function edit(User $user)
    {
        if ($user->role && $user->role->name === 'Superadmin') abort(403);
        return view('users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        return redirect()->route('users.index')->with('success', 'Biodata user berhasil diperbarui!');
    }

    public function destroy(User $user)
    {
        // PROTEKSI: Superadmin gak boleh dihapus biar sistem gak mati
        if ($user->role?->name === 'Superadmin') {
            return redirect()->back()->with('error', 'Gak bisa hapus akun Superadmin, bro!');
        }

        $user->delete();
        return redirect()->route('users.index')->with('success', 'User berhasil dihapus!');
    }
}