<?php

namespace App\Http\Controllers;

use App\Models\Reservation; // Pastikan ini juga ada
use App\Models\Room;        // INI YANG KURANG, BRO!
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReservationController extends Controller
{
    /**
     * Menampilkan form reservasi dan daftar kamar yang tersedia
     */
    public function index()
    {
        // Hanya ambil kamar yang statusnya 'available'
        $rooms = Room::where('status', 'available')->get();
        return view('reservations.index', compact('rooms'));
    }

    /**
     * Menyimpan data reservasi ke database
     */
    public function store(Request $request)
    {
        // 1. Validasi input sesuai field di UI
        $request->validate([
            'room_id'       => 'required|exists:rooms,id',
            'guest_name'    => 'required|string|max:255',
            'num_guests'    => 'required|integer|min:1',
            'guest_email'   => 'required|email',
            'guest_phone'   => 'required|string',
            'check_in'      => 'required|date',
            'check_out'     => 'required|date|after:check_in',
        ]);

        // 2. Ambil data kamar untuk mendapatkan harga per malam
        $room = Room::findOrFail($request->room_id);
        
        // 3. Hitung durasi menginap menggunakan Carbon
        $start = Carbon::parse($request->check_in);
        $end = Carbon::parse($request->check_out);
        $duration = $start->diffInDays($end);
        
        // Pastikan durasi minimal 1 hari untuk perhitungan harga
        $days = $duration <= 0 ? 1 : $duration;
        
        // 4. Hitung total harga
        $total_price = $room->price * $days;

        // 5. Simpan data reservasi
        Reservation::create([
            'room_id'       => $request->room_id,
            'guest_name'    => $request->guest_name,
            'num_guests'    => $request->num_guests,
            'guest_email'   => $request->guest_email,
            'guest_phone'   => $request->guest_phone,
            'check_in'      => $request->check_in,
            'check_out'     => $request->check_out,
            'total_price'   => $total_price,
        ]);

        // 6. Update status kamar menjadi 'booked' agar tidak bisa dipesan lagi
        $room->update(['status' => 'booked']);

        return redirect()->route('reservations.index')->with('success', 'Reservasi berhasil dibuat untuk ' . $request->guest_name);
    }
}