<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
{
    $menus = [
        ['name' => 'Dashboard', 'url' => '/dashboard'],
        ['name' => 'Data Kamar', 'url' => '/kamar'],
        ['name' => 'Reservasi', 'url' => '/reservasi'],
        ['name' => 'Laporan Keuangan', 'url' => '/laporan'],
        ['name' => 'User Management', 'url' => '/users'],
    ];

    foreach ($menus as $menu) {
        \App\Models\Menu::create($menu);
    }
}
}
