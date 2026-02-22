<?php

namespace App\Http\Controllers;

use App\Models\Guest; 
use Illuminate\Http\Request;
use App\Models\Reservation;

class GuestController extends Controller
{
    public function index()
    {
        // Tetap gunakan filter ini agar yang sudah checked_out tidak muncul
        $guests = Guest::where(function($query) {
            $query->where('status', '!=', 'checked_out')
                  ->orWhereNull('status');
        })->get(); 

        return view('guests.index', compact('guests')); 
    }

    public function toggleIncognito($id)
    {
        $guest = Guest::findOrFail($id);
        $guest->is_incognito = !$guest->is_incognito;
        $guest->save();

        return back()->with('success', 'Status privasi berhasil diperbarui!');
    }

    /**
     * Fungsi Checkout yang sudah diperbaiki otomatisasinya
     */
  public function processCheckout(Request $request, $id)
{
    // 1. Ambil data reservasi
    $reservation = Reservation::findOrFail($id);
    
    // 2. Update Status Reservasi
    $reservation->status = 'checked_out';
    $reservation->additional_charges = $request->input('additional_charges', 0);
    $reservation->notes = $request->input('notes');
    $reservation->save();

    // 3. SINKRONISASI: Kita cari tamu berdasarkan EMAIL (Jauh lebih akurat dari Nama)
    // Trim digunakan untuk menghapus spasi liar yang mungkin ada di database
    $emailTamu = trim($reservation->email);
    $guest = Guest::where('email', $emailTamu)->first();
    
    if ($guest) {
        $guest->status = 'checked_out';
        $guest->save();
    } else {
        // JALAN PINTAS JIKA EMAIL TIDAK KETEMU: Cari pakai Nama (Case Insensitive)
        $namaTamu = trim($reservation->guest_name);
        $guestByName = Guest::where('guest_name', 'LIKE', $namaTamu)->first();
        if ($guestByName) {
            $guestByName->status = 'checked_out';
            $guestByName->save();
        }
    }

    // 4. Update Status Kamar
    if ($reservation->room) {
        $reservation->room->update(['status' => 'available']);
    }

    return redirect()->route('guests.index')->with('success', 'Checkout berhasil dan daftar manajemen tamu telah diperbarui.');
}
public function bulkDelete(Request $request)
{
    $ids = explode(',', $request->ids);
    \App\Models\Guest::whereIn('id', $ids)->delete();

    return back()->with('success', count($ids) . ' data tamu berhasil dihapus!');
}
}