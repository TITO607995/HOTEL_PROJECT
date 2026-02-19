<?php

namespace App\Http\Controllers;

use App\Models\Checkout; // Gue ganti jadi Checkout sesuai sidebar VS Code lo
use App\Models\Reservation;
use Illuminate\Http\Request;
use Carbon\Carbon;

class TransactionController extends Controller
{
    /**
     * Menampilkan laporan keuangan
     */
    public function index()
    {
        // 1. Ambil data checkout dengan relasi reservation dan room
        $transactions = Checkout::with(['reservation.room'])
                        ->orderBy('checkout_at', 'desc')
                        ->paginate(10);

        // 2. Hitung statistik untuk kartu-kartu UI
        $totalPendapatan = Checkout::sum('total_amount');
        
        // Pendapatan Kamar (Total dikurangi biaya tambahan)
        $pendapatanKamar = Checkout::all()->sum(function($t) {
            return $t->total_amount - $t->additional_charges;
        });

        $pendapatanTambahan = Checkout::sum('additional_charges');
        $totalTransaksi = Checkout::count();

        // 3. Kirim ke View
        return view('reports.financial', compact(
            'transactions',
            'totalPendapatan',
            'pendapatanKamar',
            'pendapatanTambahan',
            'totalTransaksi'
        ));
    }

    /**
     * Hapus satu data
     */
    public function destroy($id)
    {
        $checkout = Checkout::findOrFail($id);
        $checkout->delete();

        return back()->with('success', 'Data transaksi berhasil dihapus.');
    }

    /**
     * Hapus banyak data sekaligus
     */
    public function bulkDelete(Request $request)
    {
        if (!$request->ids) {
            return back()->with('error', 'Pilih data yang ingin dihapus.');
        }

        $ids = explode(',', $request->ids);
        Checkout::whereIn('id', $ids)->delete();

        return back()->with('success', count($ids) . ' data berhasil dihapus.');
    }
}