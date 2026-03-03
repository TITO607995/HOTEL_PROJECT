<?php

namespace App\Http\Controllers;

use App\Models\Checkout; 
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
        // 1. Ambil data checkout dengan relasi
        $transactions = Checkout::with(['reservation.room'])
                        ->orderBy('checkout_at', 'desc')
                        ->paginate(10);

        // 2. Hitung statistik
        $totalPendapatan = Checkout::sum('total_amount');
        
        $pendapatanKamar = Checkout::all()->sum(function($t) {
            return $t->total_amount - $t->additional_charges;
        });

        $pendapatanTambahan = Checkout::sum('additional_charges');
        $totalTransaksi = Checkout::count();

        return view('reports.financial', compact(
            'transactions',
            'totalPendapatan',
            'pendapatanKamar',
            'pendapatanTambahan',
            'totalTransaksi'
        ));
    }

    /**
     * Fitur Ekspor Excel (Tanpa Library)
     */
    public function exportExcel()
    {
        $transactions = Checkout::with(['reservation.room'])->get();
        $fileName = "Laporan_Keuangan_" . date('d_M_Y') . ".xls";

        header("Content-Type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=\"$fileName\"");
        header("Pragma: no-cache");
        header("Expires: 0");

        echo '<table border="1">
                <tr style="background-color: #800000; color: white;">
                    <th>No</th>
                    <th>Tanggal C/O</th>
                    <th>Nama Tamu</th>
                    <th>Kamar</th>
                    <th>Tarif Kamar</th>
                    <th>Tambahan</th>
                    <th>Total Dibayar</th>
                </tr>';

        foreach ($transactions as $index => $trx) {
            $tarifKamar = $trx->total_amount - $trx->additional_charges;
            echo '<tr>
                    <td>' . ($index + 1) . '</td>
                    <td>' . ($trx->checkout_at ? date('d/m/Y H:i', strtotime($trx->checkout_at)) : '-') . '</td>
                    <td>' . strtoupper($trx->reservation->guest_name ?? '-') . '</td>
                    <td>' . ($trx->reservation->room->room_number ?? '-') . '</td>
                    <td>' . $tarifKamar . '</td>
                    <td>' . $trx->additional_charges . '</td>
                    <td>' . $trx->total_amount . '</td>
                  </tr>';
        }
        echo '</table>';
        exit;
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