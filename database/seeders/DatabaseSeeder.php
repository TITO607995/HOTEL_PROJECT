<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
        {
            // 1. Buat Daftar Menu
            \App\Models\Menu::insert([
                ['name' => 'Data Kamar', 'url' => '/data-kamar'],
                ['name' => 'Reservasi', 'url' => '/reservasi'],
                ['name' => 'Laporan Keuangan', 'url' => '/laporan-keuangan'],
                ['name' => 'User Management', 'url' => '/users'],
                ['name' => 'Role Management', 'url' => '/roles'],
                ['name' => 'Assign Role', 'url' => '/assign-role'],
            ]);
        
            // 2. Buat Role Superadmin
            $role = \App\Models\Role::firstOrCreate(['name' => 'Superadmin']);
        
            // 3. Buat User Admin Sakti
            \App\Models\User::firstOrCreate(
                ['email' => 'admintito@hotel.com'],
                [
                    'name' => 'Admin Hotel',
                    'password' => bcrypt('tito123'),
                    'role_id' => $role->id
                ]
            );
        }
}
