<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class EmployeeController extends Controller
{
    // 1. TAMPILKAN DAFTAR KARYAWAN
    public function index()
    {
        // Ambil user beserta nama role-nya
        $employees = User::with('role')->latest()->get();
        return view('employees.index', compact('employees'));
    }

    // 2. FORM TAMBAH KARYAWAN
    public function create()
    {
        $roles = Role::all();
        return view('employees.create', compact('roles'));
    }

    // 3. PROSES SIMPAN
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6', // Minimal 6 biar aman
            'role_id' => 'required|exists:roles,id'
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password), // Pakai Hash standard Laravel
            'role_id' => $request->role_id,
        ]);

        return redirect()->route('employees.index')->with('success', 'Karyawan baru berhasil ditambahkan!');
    }

    // 4. FORM EDIT KARYAWAN (Ganti Role / Reset Password)
    public function edit($id)
    {
        $employee = User::findOrFail($id);
        $roles = Role::all();
        return view('employees.edit', compact('employee', 'roles'));
    }

    // 5. PROSES UPDATE
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email,'.$id, // Ignore email sendiri saat validasi
            'role_id' => 'required|exists:roles,id'
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role_id' => $request->role_id
        ];

        // Cek apakah password diisi (Ganti password)
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('employees.index')->with('success', 'Data karyawan berhasil diperbarui!');
    }

    // 6. HAPUS KARYAWAN
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        
        // Proteksi: Jangan hapus diri sendiri atau Superadmin utama
        if ($user->id == auth()->id()) {
             return back()->with('error', 'Anda tidak bisa menghapus akun sendiri!');
        }

        $user->delete();
        return back()->with('success', 'Karyawan berhasil dihapus.');
    }
}