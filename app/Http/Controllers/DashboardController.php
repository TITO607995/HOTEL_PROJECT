<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Ambil Username
        $username = Auth::check() ? Auth::user()->name : "Resepsionis";

        // 2. Statistik (Disesuaikan dengan kartu di Dashboard Blade)
        $stats = [
            'Standard' => Room::where('type', 'Standard')->count(),
            'Deluxe'   => Room::where('type', 'Deluxe')->count(),
            'Suite'    => Room::where('type', 'Suite')->count(),
        ];

        // 3. Ambil data Room untuk Monitoring Live
        // Kita hanya mengambil kamar yang TIDAK 'available' agar fokus ke monitoring
        $rawRooms = Room::with(['reservations' => function($q) {
            $q->latest(); // Ambil reservasi terakhir untuk cek data tamu
        }])
        ->where('status', '!=', 'available') 
        ->orderBy('room_number')
        ->get();

        // 4. MAPPING Data agar sesuai dengan variabel $roomList di Blade
        $roomList = $rawRooms->map(function ($room) {
            // Ambil data reservasi terakhir jika ada
            $latestRes = $room->reservations->first();
            
            // Logika Warna Badge Action
            $color = match ($room->status) {
                'occupied'     => 'bg-red-600',      // Merah untuk terisi
                'booked'       => 'bg-blue-500',     // Biru untuk booking
                'vacant dirty' => 'bg-yellow-500',   // Kuning untuk kotor
                'oo'           => 'bg-black',        // Hitam untuk Rusak Berat
                'os'           => 'bg-gray-600',     // Abu tua untuk Service
                default        => 'bg-green-500',    // Hijau (Available)
            };

            // Logika Status Kiri (Teks Miring)
            $leftStatus = match ($room->status) {
                'occupied'     => 'In-house',
                'vacant dirty' => 'Dirty',
                'booked'       => 'Booked',
                'oo', 'os'     => 'Maintenance',
                default        => '-'
            };

            // Logika Data Tamu & Pembayaran
            $guestInfo = '-';
            $isPaid = false;
            $visibility = 'Public';

            // Jika statusnya Occupied atau Booked, ambil data dari reservasi
            if ($latestRes && in_array($room->status, ['occupied', 'booked'])) {
                $guestInfo = $latestRes->payment_method; // Atau $latestRes->guest_name jika mau nama
                $isPaid = $latestRes->reservation_type == 'guaranteed';
                $visibility = $latestRes->is_incognito ? 'Incognito' : 'Public';
            } elseif ($room->status == 'oo' || $room->status == 'os') {
                $guestInfo = 'Repair'; // Info pengganti jika maintenance
            }

            return [
                'no'           => $room->room_number,
                'type'         => $room->type,
                'left_status'  => $leftStatus,
                'payment'      => $guestInfo,
                'is_paid'      => $isPaid,
                'action'       => strtoupper($room->status), // Teks tombol kanan
                'action_color' => $color,
                'visibility'   => $visibility,
            ];
        });

        // 5. Kirim ke View
        // Penting: Variable harus bernama 'roomList' dan 'stats' agar cocok dengan Blade
        return view('dashboard', compact('username', 'roomList', 'stats'));
    }
}