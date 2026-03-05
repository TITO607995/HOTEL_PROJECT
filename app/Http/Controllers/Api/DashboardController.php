<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Room;
use App\Models\Reservation;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Ambil Username
        $username = Auth::check() ? Auth::user()->name : "Resepsionis";

        // 2. Statistik (Total Kamar saja, sesuai kode web lu)
        $stats = [
            'Standard' => Room::where('type', 'Standard')->count(),
            'Deluxe'   => Room::where('type', 'Deluxe')->count(),
            'Suite'    => Room::where('type', 'Suite')->count(),
        ];

        // 3. Ambil data Room untuk Monitoring Live
        $rawRooms = Room::with(['reservations' => function($q) {
            $q->latest(); 
        }])
        ->where('status', '!=', 'available') 
        ->orderBy('room_number')
        ->get();

        // 4. MAPPING Data agar sesuai dengan variabel $roomList di Flutter
        $roomList = $rawRooms->map(function ($room) {
            $latestRes = $room->reservations->first();
            
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

            if ($latestRes && in_array($room->status, ['occupied', 'booked'])) {
                $guestInfo = $latestRes->payment_method; 
                $isPaid = $latestRes->reservation_type == 'guaranteed';
                $visibility = $latestRes->is_incognito ? 'Incognito' : 'Public';
            } elseif ($room->status == 'oo' || $room->status == 'os') {
                $guestInfo = 'Repair'; 
            }

            // GAK PERLU ngirim warna 'bg-red-600' dari Laravel ke Flutter. 
            // Flutter punya bahasanya sendiri buat nentuin warna. 
            // Jadi cukup kirim datanya aja.

            return [
                'no'           => $room->room_number,
                'type'         => $room->type,
                'left_status'  => $leftStatus,
                'payment'      => $guestInfo,
                'is_paid'      => $isPaid,
                'action'       => strtoupper($room->status), // Teks tombol kanan
                'visibility'   => $visibility,
            ];
        });

        // 5. Kirim JSON ke Flutter
        return response()->json([
            'status' => 'success',
            'data' => [
                'username' => $username,
                'stats' => $stats,
                'room_list' => $roomList // <-- Ini yang bakal ditangkap jadi tabel di HP
            ]
        ], 200);
    }
}