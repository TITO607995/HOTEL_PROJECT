<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Menu;
use App\Models\Role;
use Illuminate\Support\Facades\DB;

class MenuSeeder extends Seeder
{
    public function run()
    {
        // 1. Bersihkan data lama
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('role_menu')->truncate();
        Menu::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 2. Daftar Menu Gabungan
        $menus = [
            // --- DASHBOARD ---
            ['name' => 'Dashboard', 'route_name' => 'dashboard', 'icon' => '📊', 'order' => 1],

            // --- MANAJEMEN KAMAR (Normal 1 Centangan) ---
            ['name' => 'Manajemen Kamar', 'route_name' => 'rooms.index', 'icon' => '🛏️', 'order' => 2], 

            // --- RESERVASI (Normal 1 Centangan) ---
            ['name' => 'Reservasi', 'route_name' => 'reservations.index', 'icon' => '📅', 'order' => 3], 

            // --- DATA TAMU ---
            ['name' => 'Data Tamu', 'route_name' => 'guests.index', 'icon' => '👥', 'order' => 4], 
            ['name' => 'Data Tamu - Edit', 'route_name' => 'guests.edit', 'icon' => null, 'order' => 5], // Buat Incognito

            // --- STATUS OO/OS ---
            ['name' => 'Status OO/OS', 'route_name' => 'rooms.maintenance.page', 'icon' => '🛠️', 'order' => 6], 
            ['name' => 'Status OO/OS - Edit', 'route_name' => 'rooms.maintenance.edit', 'icon' => null, 'order' => 7], // Buat update status

            // --- MANAJEMEN KARYAWAN (FULL CRUD) ---
            ['name' => 'Manajemen Karyawan', 'route_name' => 'employees.index', 'icon' => '👔', 'order' => 8], 
            ['name' => 'Manajemen Karyawan - Create', 'route_name' => 'employees.create', 'icon' => null, 'order' => 9],
            ['name' => 'Manajemen Karyawan - Edit', 'route_name' => 'employees.edit', 'icon' => null, 'order' => 10],
            ['name' => 'Manajemen Karyawan - Delete', 'route_name' => 'employees.destroy', 'icon' => null, 'order' => 11],

            // --- ROLE & AKSES (FULL CRUD) ---
            ['name' => 'Role & Akses', 'route_name' => 'roles.index', 'icon' => '🔐', 'order' => 12], 
            ['name' => 'Role & Akses - Create', 'route_name' => 'roles.create', 'icon' => null, 'order' => 13],
            ['name' => 'Role & Akses - Edit', 'route_name' => 'roles.edit', 'icon' => null, 'order' => 14],
            ['name' => 'Role & Akses - Delete', 'route_name' => 'roles.destroy', 'icon' => null, 'order' => 15],

            // --- LAPORAN ---
            ['name' => 'Lap. Operasional', 'route_name' => 'reports.operasional', 'icon' => '📈', 'order' => 16],
            ['name' => 'Lap. Keuangan', 'route_name' => 'reports.keuangan', 'icon' => '💰', 'order' => 17],
        ];

        // 3. Masukkan ke Database
        foreach ($menus as $menuData) {
            Menu::create($menuData);
        }

        // 4. Auto Assign ke Superadmin
        $superadmin = Role::where('name', 'Superadmin')->first();
        if ($superadmin) {
            $superadmin->menus()->sync(Menu::pluck('id'));
        }
        
        // 5. Auto Assign ke Resepsionis
        $receptionist = Role::where('name', 'Resepsionis')->first();
        if ($receptionist) {
            $receptionistMenuIds = Menu::whereIn('name', [
                'Dashboard', 
                'Reservasi', 
                'Data Tamu',
                'Status OO/OS'
            ])->pluck('id');

            $receptionist->menus()->sync($receptionistMenuIds); 
        }

        $this->command->info('Daftar Menu sukses! (Kamar & Reservasi tanpa CRUD) 🎉');
    }
}