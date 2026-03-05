<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\User;
use App\Models\Checkout;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReportController extends Controller
{
    // 1. API LAPORAN OPERASIONAL
    public function operasional()
    {
        try {
            $availableCount = Room::where('status', 'available')->count();
            $ooCount        = Room::where('status', 'oo')->count();
            $staffCount     = User::count(); 
            
            $totalRooms = Room::count();
            $occupied   = Room::where('status', 'occupied')->count();
            $occupancyRate = $totalRooms > 0 ? round(($occupied / $totalRooms) * 100) : 0;

            // Log aktivitas (Diambil dari update kamar terbaru)
            $recentRooms = Room::orderBy('updated_at', 'desc')->take(10)->get();

            return response()->json([
                'status' => 'success',
                'data' => [
                    'available' => $availableCount,
                    'oo' => $ooCount,
                    'staff' => $staffCount,
                    'occupancy' => $occupancyRate,
                    'logs' => $recentRooms
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    // 2. API LAPORAN KEUANGAN
    public function keuangan()
    {
        try {
            $totalPendapatan = Checkout::sum('total_amount');
            $pendapatanTambahan = Checkout::sum('additional_charges');
            $pendapatanKamar = $totalPendapatan - $pendapatanTambahan;
            $totalTransaksi = Checkout::count();

            // Riwayat transaksi terbaru
            $transactions = Checkout::with(['reservation.room'])->latest()->take(20)->get()->map(function($t) {
                return [
                    'id' => $t->id,
                    'date' => Carbon::parse($t->created_at)->format('d M Y H:i'),
                    'guest_name' => $t->reservation ? $t->reservation->guest_name : 'Unknown',
                    'room_number' => ($t->reservation && $t->reservation->room) ? $t->reservation->room->room_number : '-',
                    'room_price' => $t->total_amount - $t->additional_charges,
                    'additional_charges' => $t->additional_charges,
                    'total_amount' => $t->total_amount
                ];
            });

            return response()->json([
                'status' => 'success',
                'data' => [
                    'total_pendapatan' => $totalPendapatan,
                    'pendapatan_kamar' => $pendapatanKamar,
                    'biaya_tambahan' => $pendapatanTambahan,
                    'total_transaksi' => $totalTransaksi,
                    'transactions' => $transactions
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}