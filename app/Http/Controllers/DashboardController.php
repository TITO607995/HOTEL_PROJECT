<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Ambil Username
        $username = Auth::check() ? Auth::user()->name : "Resepsionis";

        // 2. Statistik Kamar
        $stats = [
            'Standard' => Room::where('type', 'Standard')->count(),
            'Deluxe'   => Room::where('type', 'Deluxe')->count(),
            'Suite'    => Room::where('type', 'Suite')->count(),
        ];

        // 3. Ambil data Room (Kamar Terisi & Maintenance)
        $rooms = Room::with(['reservations' => function($q) {
                $q->latest(); 
            }])
            ->where('status', '!=', 'available') 
            ->orderBy('room_number')
            ->paginate(10);

        // 4. MAPPING Data untuk Tampilan Table
$roomList = $rooms->getCollection()->map(function ($room) {
    $latestRes = $room->reservations->first();
    
    // 1. Logika Warna & Label Status (tetap sama)
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

    $paymentMethod = '-';
    $isPaid = false;

    // 2. LOGIKA PENGECEKAN PEMBAYARAN
    if ($latestRes) {
        // Tampilkan Metode Bayar
        $paymentMethod = $latestRes->payment_method ? strtoupper($latestRes->payment_method) : '-';
        
        // --- JURUS SAPU JAGAT ---
        // Kita cek semua kemungkinan nama kolom yang mungkin kamu buat di database
        $statusBayar = strtolower($latestRes->payment_status ?? $latestRes->status ?? '');
        $cekIsPaid   = $latestRes->is_paid ?? $latestRes->paid ?? 0;

        if (
            in_array($statusBayar, ['paid', 'lunas', 'success', 'settlement', 'terbayar']) || 
            $cekIsPaid == 1 || 
            $cekIsPaid === true ||
            $cekIsPaid === "1"
        ) {
            $isPaid = true;
        }
    } 

    // 3. Status Maintenance / Dirty otomatis centang supaya tidak merah semua
    if (in_array($room->status, ['oo', 'os', 'vacant dirty'])) {
        $isPaid = true; 
    }

    return [
        'no'           => $room->room_number,
        'left_status'  => $leftStatus,
        'payment'      => $paymentMethod,
        'is_paid'      => (bool) $isPaid, 
        'action'       => strtoupper($room->status),
        'action_color' => $color,
    ];
});

        $rooms->setCollection($roomList);
        return view('dashboard', compact('username', 'stats', 'rooms'));
    }
    public function setLanguage($lang)
    {
        if (in_array($lang, ['en', 'id'])) {
            Session::put('locale', $lang);
        }
        return redirect()->back();
    }
}