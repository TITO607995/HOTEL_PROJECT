<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Ambil Username
        $username = Auth::check() ? Auth::user()->name : "Resepsionis";
        
        // 2. Ambil data Room dengan Pagination
        $rooms = Room::latest()->paginate(5);

        // 3. Mapping warna status (disinkronkan dengan tampilan tabel)
        $rooms->through(function ($room) {
            $room->color = match (strtolower($room->status)) {
                'available'   => 'bg-green-500',
                'occupied'    => 'bg-red-500',
                'dirty'       => 'bg-yellow-400',
                'maintenance' => 'bg-gray-500',
                'in-house'    => 'bg-orange-400',
                default       => 'bg-blue-500',
            };
            return $room;
        });

        // 4. Statistik Dinamis
        $statsData = Room::select('type', 
            DB::raw('count(*) as total'),
            DB::raw("SUM(CASE WHEN status = 'available' THEN 1 ELSE 0 END) as sisa")
        )
        ->groupBy('type')
        ->get();

        // Inisialisasi array default agar tidak error jika database kosong
        $stats = [
            'Standard' => ['total' => 0, 'sisa' => 0],
            'Suite'    => ['total' => 0, 'sisa' => 0],
            'Deluxe'   => ['total' => 0, 'sisa' => 0]
        ];

        foreach ($statsData as $data) {
            $type = ucfirst(strtolower($data->type)); 
            if (array_key_exists($type, $stats)) {
                $stats[$type] = [
                    'total' => (int) $data->total,
                    'sisa'  => (int) ($data->sisa ?? 0)
                ];
            }
        }

        return view('dashboard', compact('username', 'rooms', 'stats'));
    }
}