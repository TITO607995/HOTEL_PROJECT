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

        // 2. Buat Role (Pastikan huruf besar/kecil konsisten dengan Blade kita)
        $roleSuper = Role::firstOrCreate(['NAME' => 'SUPERADMIN']);
        $roleStaff = Role::firstOrCreate(['NAME' => 'STAFF']);


        // Akun Admin Galang
        User::updateOrCreate(
            ['email' => 'staff@hotel.com'],
            [
                'name' => 'Staff Hotel',
                'password' => Hash::make('Galang123'), // PW Baru
                'role_id' => $roleStaff->id
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

         User::updateOrCreate(
            ['email' => 'admin4@gmail.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('123'), // PW Baru
                'role_id' => $roleSuper->id
            ]
        );
    }
}