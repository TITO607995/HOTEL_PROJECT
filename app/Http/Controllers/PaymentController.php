<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        // 1. Ambil ID dari URL
        $reservationId = $request->query('reservation_id');
        
        // 2. Ambil data reservasi beserta data kamarnya
        $reservation = Reservation::with('room')->findOrFail($reservationId);

        // 3. Logic Hitung Durasi (Nights)
        $checkin = Carbon::parse($reservation->check_in);
        $checkout = Carbon::parse($reservation->check_out);
        $nights = $checkin->diffInDays($checkout);
        
        // Pastikan minimal 1 malam jika check-in/out di hari yang sama (day-use)
        $nights = $nights > 0 ? $nights : 1;

        // 4. Hitung Total Harga Dinamis (Harga Kamar * Malam)
        $reservation->total_price = $reservation->room->price * $nights;

        return view('payments.index', compact('reservation', 'nights'));
    }

    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'reservation_id' => 'required|exists:reservations,id',
            'payment_method' => 'required|string',
        ]);

        // 1. Cari data reservasi
        $reservation = Reservation::findOrFail($request->reservation_id);

        // 2. Update Status Reservasi
        // Kita set jadi 'checked-in' dan tandai pembayarannya
        $reservation->update([
            'status' => 'checked-in',
            'payment_status' => 'paid', // Pastikan kolom ini ada di migrasi lo
            'payment_method' => $request->payment_method,
        ]);

        // 3. Update Status Kamar jadi Terisi (Occupied)
        if ($reservation->room) {
            $reservation->room->update([
                'status' => 'occupied' // Sesuaikan dengan enum status di tabel rooms lo
            ]);
        }

        // 4. Balik ke halaman registrasi dengan pesan sukses
        return redirect()->route('reservations.registration')
            ->with('success', 'Pembayaran Berhasil! Tamu ' . $reservation->guest_name . ' resmi Check-in.');
    }
}