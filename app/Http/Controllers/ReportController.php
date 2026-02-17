<?php

namespace App\Http\Controllers;

use App\Models\Room; // Pastikan model Room di-import
use App\Models\User; // Untuk hitung staff
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index()
    {
        // Hitung data langsung dari database
        $availableCount = Room::where('status', 'available')->count();
        $ooCount        = Room::where('status', 'oo')->count();
        $staffCount     = User::count(); // Asumsi staff ada di tabel users
        
        // Hitung Persentase Occupancy (contoh sederhana)
        $totalRooms = Room::count();
        $occupied   = Room::where('status', 'occupied')->count();
        $occupancyRate = $totalRooms > 0 ? round(($occupied / $totalRooms) * 100) : 0;

        // Ambil 10 aktivitas terbaru (opsional, bisa dari tabel log jika ada)
        $recentRooms = Room::orderBy('updated_at', 'desc')->take(10)->get();

        return view('reports.index', compact(
            'availableCount', 
            'ooCount', 
            'staffCount', 
            'occupancyRate',
            'recentRooms'
        ));
    }
}