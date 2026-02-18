<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Menu;
use App\Models\Role;

class MenuSeeder extends Seeder
{
    public function run()
    {
        // 1. Daftar Menu Aplikasi Hotel SIG
        $menus = [
            [
                'name' => 'Dashboard',
                'route_name' => 'dashboard', // Pastikan route name ini ada di web.php
                'icon' => '📊',
                'order' => 1
            ],
            [
                'name' => 'Manajemen Kamar',
                'route_name' => 'rooms.index',
                'icon' => '🛏️',
                'order' => 2
            ],
            [
                'name' => 'Reservasi',
                'route_name' => 'reservations.index',
                'icon' => '📅',
                'order' => 3
            ],
            [
                'name' => 'Data Tamu',
                'route_name' => 'guests.index',
                'icon' => '👥',
                'order' => 4
            ],
            [
                'name' => 'Status OO/OS',
                'route_name' => 'rooms.maintenance.page',
                'icon' => '🛠️',
                'order' => 5
            ],
            [
                'name' => 'Manajemen Karyawan',
                'route_name' => 'employees.index',
                'icon' => '👔',
                'order' => 6
            ],
            [
                'name' => 'Role & Akses',
                'route_name' => 'roles.index',
                'icon' => '🔐',
                'order' => 7
            ],
        ];

        foreach ($menus as $menuData) {
            Menu::create($menuData);
        }

        // 2. Auto Assign semua menu ke Superadmin (Role ID 1)
        $superadmin = Role::where('name', 'Superadmin')->first();
        if ($superadmin) {
            // Ambil semua ID menu dan masukkan ke tabel pivot role_menu
            $superadmin->menus()->sync(Menu::pluck('id'));
        }
        
        // 3. Auto Assign menu dasar ke Resepsionis (Role ID 2 - Opsional)
        $receptionist = Role::where('name', 'Resepsionis')->first();
        if ($receptionist) {
            // Hanya menu Dashboard, Reservasi, Tamu, dan OO/OS
            $receptionist->menus()->sync([1, 2, 3, 4, 5]); 
        }
    }
}