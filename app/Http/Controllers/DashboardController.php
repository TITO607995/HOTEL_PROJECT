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

    // 2. Statistik
    $stats = [
        'Standard' => Room::where('type', 'Standard')->count(),
        'Deluxe'   => Room::where('type', 'Deluxe')->count(),
        'Suite'    => Room::where('type', 'Suite')->count(),
    ];

    // 3. Ambil data Room dengan PAGINATE (Jangan pakai ->get())
    $rooms = Room::with(['reservations' => function($q) {
            $q->latest(); 
        }])
        ->where('status', '!=', 'available') 
        ->orderBy('room_number')
        ->paginate(10); // <--- Kuncinya di sini

    // 4. MAPPING melalui Collection (Tapi tetap pertahankan objek paginatornya)
    // Kita buat variabel baru $roomList untuk di looping di Blade
    $roomList = $rooms->getCollection()->map(function ($room) {
        $latestRes = $room->reservations->first();
        
        $color = match ($room->status) {
            'occupied'     => 'bg-red-600',
            'booked'       => 'bg-blue-500',
            'vacant dirty' => 'bg-yellow-500',
            'oo'           => 'bg-black',
            'os'           => 'bg-gray-600',
            default        => 'bg-green-500',
        };

        $leftStatus = match ($room->status) {
            'occupied'     => 'In-house',
            'vacant dirty' => 'Dirty',
            'booked'       => 'Booked',
            'oo', 'os'     => 'Maintenance',
            default        => '-'
        };

        $guestInfo = '-';
        $isPaid = false;

        if ($latestRes && in_array($room->status, ['occupied', 'booked'])) {
            $guestInfo = $latestRes->payment_method;
            $isPaid = $latestRes->reservation_type == 'guaranteed';
        } elseif ($room->status == 'oo' || $room->status == 'os') {
            $guestInfo = 'Repair';
        }

        return [
            'no'           => $room->room_number,
            'left_status'  => $leftStatus,
            'payment'      => $guestInfo,
            'is_paid'      => $isPaid,
            'action'       => strtoupper($room->status),
            'action_color' => $color,
        ];
    });

    // Masukkan kembali collection yang sudah di-map ke dalam objek paginator
    $rooms->setCollection($roomList);

    // 5. Kirim ke View (Gunakan $rooms untuk links dan looping)
    return view('dashboard', compact('username', 'stats', 'rooms'));
    }
}