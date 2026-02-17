<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function create()
{
    // Mengambil semua role (SUPERADMIN, STAFF, dll) dari database
    $roles = \App\Models\Role::all();
    return view('employees.create', compact('roles'));
}

public function store(Request $request)
{
    $request->validate([
        'name' => 'required',
        'email' => 'required|email|unique:users',
        'password' => 'required|min:3',
        'role_id' => 'required'
    ]);

    \App\Models\User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => bcrypt($request->password),
        'role_id' => $request->role_id,
    ]);

    return redirect()->route('dashboard')->with('success', 'Karyawan baru berhasil ditambahkan!');
}
}
