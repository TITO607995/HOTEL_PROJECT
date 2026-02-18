<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;

class AssignRoleController extends Controller
{
    public function index()
    {
        $users = User::with('role')->get();
        return view('assign-role.index', compact('users'));
    }

    public function edit(User $user)
    {
        // PERBAIKAN: Cek 'Superadmin' dan 'SUPERADMIN' jaga-jaga
        if ($user->role && strtoupper($user->role->name) === 'SUPERADMIN') {
            return redirect()->back()->with('error', 'Role Superadmin sudah paten, bro!');
        }

        $roles = Role::all();
        return view('assign-role.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate(['role_id' => 'required|exists:roles,id']);
        $user->update(['role_id' => $request->role_id]);
        return redirect()->route('assign-role.index')->with('success', "Role {$user->name} berhasil diperbarui!");
    }
}