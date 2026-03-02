<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\User;
use App\Models\Checkout;
use Illuminate\Http\Request;
use App\Models\Transaction;

class ReportController extends Controller
{
    // 1. LAPORAN OPERASIONAL (YANG LAMA)
    public function index()
    {
        $availableCount = Room::where('status', 'available')->count();
        $ooCount        = Room::where('status', 'oo')->count();
        $staffCount     = User::count(); 
        
        $totalRooms = Room::count();
        $occupied   = Room::where('status', 'occupied')->count();
        $occupancyRate = $totalRooms > 0 ? round(($occupied / $totalRooms) * 100) : 0;

        $recentRooms = Room::orderBy('updated_at', 'desc')->take(10)->get();

        return view('reports.index', compact(
            'availableCount', 'ooCount', 'staffCount', 'occupancyRate', 'recentRooms'
        ));
    }

    // 2. LAPORAN KEUANGAN (YANG BARU)
    public function financial()
    {
        $transactions = Checkout::with(['reservation.room'])->latest()->paginate(10);

        $totalPendapatan = Checkout::sum('total_amount');
        $pendapatanTambahan = Checkout::sum('additional_charges');
        $pendapatanKamar = $totalPendapatan - $pendapatanTambahan;
        $totalTransaksi = Checkout::count();

        // Kita arahkan ke view yang berbeda: reports.financial
        return view('reports.financial', compact(
            'transactions', 'totalPendapatan', 'pendapatanTambahan', 'pendapatanKamar', 'totalTransaksi'
        ));
    }
}