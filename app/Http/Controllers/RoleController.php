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
        $all_menus = Menu::all();
        return view('roles.create', compact('all_menus'));
    }

    public function store(Request $request) {
        $request->validate(['name' => 'required']);
        
        // Simpan ke kolom NAME (besar) agar sinkron dengan seeder
        $role = Role::create(['NAME' => strtoupper($request->name)]);
        
        if ($request->has('menu_ids')) {
            $role->menus()->sync($request->menu_ids);
        }
        return redirect()->route('roles.index')->with('success', 'Role baru berhasil dibuat!');
    }

    public function edit(Role $role) {
        if ($role->NAME === 'SUPERADMIN') abort(403);
        $all_menus = Menu::all();
        return view('roles.edit', compact('role', 'all_menus'));
    }

    public function update(Request $request, Role $role) {
        $role->update(['NAME' => strtoupper($request->name)]);
        $role->menus()->sync($request->menu_ids ?? []);
        return redirect()->route('roles.index')->with('success', 'Akses Role berhasil diupdate!');
    }

    public function destroy(Role $role)
    {
        if ($role->NAME === 'SUPERADMIN') {
            return redirect()->back()->with('error', 'Role Superadmin tidak bisa dihapus!');
        }
        $role->delete();
        return redirect()->route('roles.index')->with('success', 'Role berhasil dihapus!');
    }
}