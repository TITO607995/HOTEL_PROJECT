<?php

namespace App\Exports;

use App\Models\Transaction;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class FinancialExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        // Mengambil semua data transaksi beserta relasinya
        return Transaction::with(['reservation.room'])->get();
    }

    // Header Tabel di Excel
    public function headings(): array
    {
        return [
            'ID Transaksi',
            'Tanggal Check-out',
            'Nama Tamu',
            'Nomor Kamar',
            'Tarif Kamar',
            'Biaya Tambahan',
            'Total Dibayar',
            'Catatan'
        ];
    }

    // Mapping data agar rapi di Excel
    public function map($trx): array
    {
        return [
            $trx->id,
            $trx->checkout_at,
            $trx->reservation->guest_name ?? '-',
            $trx->reservation->room->room_number ?? '-',
            $trx->total_amount - $trx->additional_charges,
            $trx->additional_charges,
            $trx->total_amount,
            $trx->notes
        ];
    }
}