<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Menu;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index() {
        $roles = Role::with('menus')->get();
        return view('roles.index', compact('roles'));
    }

    public function create() {
        $all_menus = Menu::orderBy('order')->get(); // Diurutkan biar rapi
        return view('roles.create', compact('all_menus'));
    }

    public function store(Request $request) {
        $request->validate(['name' => 'required|unique:roles,name']);
        
        // PERBAIKAN: Array key pakai huruf kecil 'name'
        // Kita simpan pakai huruf Kapital (strtoupper) biar konsisten
        $role = Role::create(['name' => strtoupper($request->name)]);
        
        if ($request->has('menu_ids')) {
            $role->menus()->sync($request->menu_ids);
        }
        return redirect()->route('roles.index')->with('success', 'Role baru berhasil dibuat!');
    }

    public function edit(Role $role) {
        // Proteksi Superadmin (Case sensitive check)
        if ($role->name === 'SUPERADMIN' || $role->name === 'Superadmin') {
             return redirect()->route('roles.index')->with('error', 'Role Superadmin tidak boleh diedit!');
        }
        
        $all_menus = Menu::orderBy('order')->get();
        return view('roles.edit', compact('role', 'all_menus'));
    }

    public function update(Request $request, Role $role) {
        // Proteksi Superadmin lagi
        if ($role->name === 'SUPERADMIN') abort(403);

        $role->update(['name' => strtoupper($request->name)]);
        $role->menus()->sync($request->menu_ids ?? []);
        
        return redirect()->route('roles.index')->with('success', 'Akses Role berhasil diupdate!');
    }

    public function destroy(Role $role)
    {
        if ($role->name === 'SUPERADMIN' || $role->name === 'Superadmin') {
            return redirect()->back()->with('error', 'Role Superadmin tidak bisa dihapus!');
        }
        
        // Cek apakah ada user yang pakai role ini
        if ($role->users()->count() > 0) {
            return back()->with('error', 'Gagal! Masih ada karyawan yang menggunakan role ini.');
        }

        $role->delete();
        return redirect()->route('roles.index')->with('success', 'Role berhasil dihapus!');
    }
}