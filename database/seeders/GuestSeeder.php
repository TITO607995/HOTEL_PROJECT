<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Reservation;
use App\Models\Guest;

class GuestSeeder extends Seeder
{
    /**
     * Jalankan database seeds.
     * Kode ini akan menyalin data dari tabel reservations ke tabel guests.
     */
    public function run(): void
    {
        // 1. Ambil semua data dari tabel reservations
        $reservations = Reservation::all();

        // 2. Loop dan masukkan ke tabel guests
        foreach ($reservations as $res) {
            Guest::updateOrCreate(
                // Unik berdasarkan email agar tidak ada tamu ganda
                ['email' => $res->email], 
                [
                    'guest_name'   => $res->guest_name,
                    'is_incognito' => false, // Default awal adalah publik
                ]
            );
        }

        $this->command->info('Sukses! Data tamu dari tabel reservasi telah disinkronkan ke tabel Guest.');
    }
}