<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // 1. Ambil User + Relasi Role-nya
    public function index()
    {
        $users = User::with('role')->latest()->get();
        return response()->json(['status' => 'success', 'data' => $users], 200);
    }

    // 2. Ambil Semua Role dari Database
    public function getRoles()
    {
        $roles = Role::all();
        return response()->json(['status' => 'success', 'data' => $roles], 200);
    }

    // 3. Tambah User (Pake role_id)
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'role_id' => 'required|exists:roles,id'
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $request->role_id,
        ]);

        return response()->json(['status' => 'success', 'message' => 'User berhasil ditambahkan!']);
    }

    // 4. Assign Role
    public function assignRole(Request $request, $id)
    {
        $user = User::with('role')->findOrFail($id);

        // PROTEKSI: Superadmin gak boleh diganti rolenya
        if ($user->role && strtoupper($user->role->name) === 'SUPERADMIN') {
            return response()->json(['status' => 'error', 'message' => 'Role Superadmin sudah paten!'], 403);
        }

        $user->update(['role_id' => $request->role_id]);
        return response()->json(['status' => 'success', 'message' => 'Role berhasil diperbarui!']);
    }

    // 5. Hapus User
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        // PROTEKSI: Superadmin gak boleh dihapus
        if ($user->role && strtoupper($user->role->name) === 'SUPERADMIN') {
            return response()->json(['status' => 'error', 'message' => 'Akun Superadmin tidak boleh dihapus!'], 403);
        }

        // PROTEKSI: Gak boleh hapus diri sendiri
        if ($user->id == auth()->id()) {
            return response()->json(['status' => 'error', 'message' => 'Tidak bisa menghapus akun sendiri!'], 403);
        }

        $user->delete();
        return response()->json(['status' => 'success', 'message' => 'User berhasil dihapus!']);
    }
}