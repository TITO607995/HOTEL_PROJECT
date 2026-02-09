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
        $role = Role::create(['name' => $request->name]);
        if ($request->has('menu_ids')) {
            // Ini yang nge-save checklist menu ke role
            $role->menus()->sync($request->menu_ids);
        }
        return redirect()->route('roles.index')->with('success', 'Role baru berhasil dibuat!');
    }

    public function edit(Role $role) {
        if ($role->name === 'Superadmin') abort(403);
        $all_menus = Menu::all();
        return view('roles.edit', compact('role', 'all_menus'));
    }

    public function update(Request $request, Role $role) {
        $role->update(['name' => $request->name]);
        // Update checklist menu (sync otomatis nambah/hapus yang nggak di-ceklis)
        $role->menus()->sync($request->menu_ids ?? []);
        return redirect()->route('roles.index')->with('success', 'Akses Role berhasil diupdate!');
    }

    public function destroy(Role $role)
    {
        if ($role->name === 'Superadmin') {
            return redirect()->back()->with('error', 'Role Superadmin tidak bisa dihapus, bro!');
        }
        $role->delete();

        return redirect()->route('roles.index')->with('success', 'Role berhasil dihapus dari sistem!');
    }
}