<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RoomSeeder extends Seeder
{
    public function run(): void
    {
        // Konfigurasi jumlah dan harga
        $roomTypes = [
            'standard' => ['count' => 15, 'price' => 350000, 'prefix' => '1'],
            'deluxe'   => ['count' => 10, 'price' => 550000, 'prefix' => '2'],
            'suite'    => ['count' => 5,  'price' => 950000, 'prefix' => '3'],
        ];

        foreach ($roomTypes as $type => $info) {
            for ($i = 1; $i <= $info['count']; $i++) {
                // Format nomor kamar: prefix + urutan (contoh: 101, 102, dst)
                $roomNumber = $info['prefix'] . str_pad($i, 2, '0', STR_PAD_LEFT);

                DB::table('rooms')->insert([
                    'room_number' => $roomNumber,
                    'type'        => $type,
                    'price'       => $info['price'],
                    'status'      => 'available', // Default tersedia semua
                    'maintenance_type' => null,
                    'maintenance_notes' => null,
                    'created_at'  => Carbon::now(),
                    'updated_at'  => Carbon::now(),
                ]);
            }
        }
    }
}