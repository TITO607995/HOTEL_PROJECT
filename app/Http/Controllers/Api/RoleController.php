<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\Menu;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    // 1. Ambil Semua Role & Daftar Menu
    public function index()
    {
        $roles = Role::with('menus')->get();
        $menus = Menu::orderBy('order')->get(); // Ambil semua menu buat pilihan Checkbox

        return response()->json([
            'status' => 'success',
            'data' => [
                'roles' => $roles,
                'menus' => $menus
            ]
        ], 200);
    }

    // 2. Bikin Role Baru & Assign Menu
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:roles,name',
            'menu_ids' => 'array' // Array ID menu yang dicentang
        ]);

        try {
            $role = Role::create(['name' => strtoupper($request->name)]);
            
            if ($request->has('menu_ids')) {
                $role->menus()->sync($request->menu_ids);
            }

            return response()->json(['status' => 'success', 'message' => 'Role baru berhasil dibuat!']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    // 3. Update Role & Assign Menu
    public function update(Request $request, $id)
    {
        try {
            $role = Role::findOrFail($id);

            if (strtoupper($role->name) === 'SUPERADMIN') {
                return response()->json(['status' => 'error', 'message' => 'Role Superadmin tidak boleh diedit!'], 403);
            }

            $role->update(['name' => strtoupper($request->name)]);
            $role->menus()->sync($request->menu_ids ?? []); // Update menu yang dicentang
            
            return response()->json(['status' => 'success', 'message' => 'Akses Role berhasil diupdate!']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    // 4. Hapus Role
    public function destroy($id)
    {
        try {
            $role = Role::findOrFail($id);

            if (strtoupper($role->name) === 'SUPERADMIN') {
                return response()->json(['status' => 'error', 'message' => 'Role Superadmin tidak bisa dihapus!'], 403);
            }
            
            if ($role->users()->count() > 0) {
                return response()->json(['status' => 'error', 'message' => 'Gagal! Masih ada karyawan yang menggunakan role ini.'], 403);
            }

            $role->delete();
            return response()->json(['status' => 'success', 'message' => 'Role berhasil dihapus!']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}