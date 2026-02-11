<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use App\Models\Menu;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // 1. Buat Daftar Menu (Master Data)
        // Pakai collect()->each agar tidak duplikat saat dijalankan ulang
        $menus = [
            ['name' => 'Data Kamar', 'url' => '/data-kamar'],
            ['name' => 'Reservasi', 'url' => '/reservasi'],
            ['name' => 'Laporan Keuangan', 'url' => '/laporan-keuangan'],
            ['name' => 'User Management', 'url' => '/users'],
            ['name' => 'Role Management', 'url' => '/roles'],
            ['name' => 'Assign Role', 'url' => '/assign-role'],
        ];

        foreach ($menus as $menu) {
            Menu::firstOrCreate(['name' => $menu['name']], $menu);
        }

        // 2. Buat Role (Pastikan huruf besar/kecil konsisten dengan Blade kita)
        $roleSuper = Role::firstOrCreate(['NAME' => 'SUPERADMIN']);
        $roleStaff = Role::firstOrCreate(['NAME' => 'STAFF']);

        // 3. Buat User Admin Sakti (Update Email & PW sesuai request)
        // Akun Utama (CEO/Tito)
        User::updateOrCreate(
            ['email' => 'admintito@hotel.com'],
            [
                'name' => 'Tito Executive',
                'password' => Hash::make('tito123'), // PW Baru
                'role_id' => $roleSuper->id
            ]
        );

        // Akun Admin Galang
        User::updateOrCreate(
            ['email' => 'admin@hotel.com'],
            [
                'name' => 'Admin Galang',
                'password' => Hash::make('Galang123'), // PW Baru
                'role_id' => $roleSuper->id
            ]
        );

         User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Admin Galang',
                'password' => Hash::make('password123'), // PW Baru
                'role_id' => $roleSuper->id
            ]
        );

        // 4. Tambahan: Buat User Staff Biasa (Untuk ngetes proteksi sistem)
        User::updateOrCreate(
            ['email' => 'staff@hotel.com'],
            [
                'name' => 'Staff Biasa',
                'password' => Hash::make('staff123'),
                'role_id' => $roleStaff->id
            ]
        );
        // 4. Tambahan: Buat User Staff Biasa (Untuk ngetes proteksi sistem)
        User::updateOrCreate(
            ['email' => 'staff12@hotel.com'],
            [
                'name' => 'Staff Biasa 2',
                'password' => Hash::make('staff123'),
                'role_id' => $roleStaff->id
            ]
        );
    }
}