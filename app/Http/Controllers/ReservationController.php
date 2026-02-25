<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Room;
use App\Models\Checkout;
use App\Models\Guest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReservationController extends Controller
{
    // 1. HALAMAN DAFTAR RESERVASI
    public function index()
    {
        $rooms = Room::all(); 
        $reservations = Reservation::with('room')->latest()->get();
        return view('reservations.index', compact('reservations', 'rooms'));
    }

    // 2. FORM TAMBAH RESERVASI
    public function create()
    {
        $rooms = Room::where('status', 'available')->get();
        return view('reservations.create', compact('rooms'));
    }

    // 3. PROSES SIMPAN RESERVASI BARU (LANGSUNG SYNC KE GUEST & REDIRECT)
    public function store(Request $request)
    {
        $request->validate([
            'room_id'        => 'required|exists:rooms,id',
            'guest_name'     => 'required|string|max:255',
            'arrival_date'   => 'required|date',
            'departure_date' => 'required|date|after:arrival_date',
            'email'          => 'required|email',
            'phone'          => 'required',
        ]);

        DB::transaction(function () use ($request) {
            // A. Simpan data Reservasi
            Reservation::create([
                'room_id'          => $request->room_id,
                'guest_name'       => $request->guest_name,
                'num_guests'       => $request->num_guests ?? 1,
                'email'            => $request->email,
                'phone'            => $request->phone,
                'arrival_date'     => $request->arrival_date,
                'departure_date'   => $request->departure_date,
                'payment_method'   => $request->payment_method ?? 'Cash',
                'reservation_type' => $request->reservation_type ?? 'non-guaranteed',
                'country'          => $request->country,
                'city'             => $request->city,
                'place_birth'      => $request->place_birth,
                'status'           => 'booked',
            ]);

            // B. LANGSUNG INPUT KE TABEL GUEST (Ini yang lo minta)
            Guest::updateOrCreate(
                ['email' => $request->email], // Cari berdasarkan email
                [
                    'guest_name'   => $request->guest_name,
                    'is_incognito' => false
                ]
            );

            // C. Update status kamar
            Room::where('id', $request->room_id)->update(['status' => 'booked']);
        });

        // D. REDIRECT LANGSUNG KE HALAMAN GUEST
        return redirect()->route('guests.index')->with('success', 'Reservasi berhasil & data tamu telah diperbarui!');
    }

    // 4. HALAMAN CHECK-IN
    public function registration()
    {
        $reservations = Reservation::with('room')
                        ->where(function($query) {
                            $query->where('status', 'booked')->orWhereNull('status'); 
                        })->latest()->get();
                        
        return view('reservations.registration', compact('reservations'));
    }

    // 5. PROSES CHECK-IN (Update status reservasi + update status kamar jadi occupied)
 public function checkin($id)
{
    DB::transaction(function () use ($id) {
        $reservation = Reservation::with('room')->findOrFail($id);

        // Update status reservasi (opsional, sesuaikan dengan enum di DB kamu)
        $reservation->update(['status' => 'checked-in']);

        // Update status di tabel rooms
        $reservation->room->update([
            'status' => 'occupied', // Ubah jadi occupied
            'guest_id' => $reservation->guest_id // Hubungkan ke tamu
        ]);
    });

    return redirect()->back()->with('success', 'Tamu telah masuk kamar (Occupied)!');
}
    // 6. HALAMAN CHECK-OUT
    public function checkoutPage()
    {
        $reservations = Reservation::where('status', 'checked-in')->with('room')->get();
        return view('reservations.checkout', compact('reservations'));
    }

    // 7. DETAIL CHECK-OUT
    public function checkout($id)
    {
        $reservation = Reservation::with('room')->findOrFail($id);
        $checkIn = Carbon::parse($reservation->arrival_date);
        $checkOut = Carbon::parse($reservation->departure_date);
        $nights = $checkIn->diffInDays($checkOut) ?: 1;

        $hargaPerMalam = $reservation->room->price ?? 500000; 
        $roomCharge = $hargaPerMalam * $nights;

        return view('reservations.checkout-detail', compact('reservation', 'nights', 'roomCharge', 'hargaPerMalam'));
    }

    // 8. PROSES FINAL CHECK-OUT
    public function processCheckout(Request $request, $id)
    {
        $res = Reservation::with('room')->findOrFail($id);
        $nights = Carbon::parse($res->arrival_date)->diffInDays(Carbon::parse($res->departure_date)) ?: 1;
        $totalAmount = ($res->room->price * $nights) + ($request->additional_charges ?? 0);

        Checkout::create([
            'reservation_id' => $res->id,
            'additional_charges' => $request->additional_charges ?? 0,
            'notes' => $request->notes,
            'total_amount' => $totalAmount,
            'checkout_at' => now(),
        ]);

        $res->room->update(['status' => 'vacant dirty']);
        $res->update(['status' => 'checked-out']);

        return redirect()->route('reservations.index')->with('success', 'Check-out berhasil!');
    }

    // 9. EXTEND MASA INAP
    // FUNGSI PERPANJANG MENGINAP (EXTEND)
    // FUNGSI UBAH TANGGAL CHECK-OUT (EXTEND/REDUCE)
    public function extend(Request $request, $id)
    {
        $request->validate([
            'new_departure_date' => 'required|date',
        ]);

        $reservation = Reservation::findOrFail($id);
        
        $newDate = \Carbon\Carbon::parse($request->new_departure_date);
        $arrivalDate = \Carbon\Carbon::parse($reservation->arrival_date);

        // Validasi: Pastikan tanggal kepulangan minimal 1 hari setelah Check-in
        if ($newDate->lte($arrivalDate)) {
            return redirect()->back()->with('error', 'Gagal! Tanggal Check-out tidak boleh mundur melebih tanggal Check-in (' . $arrivalDate->format('d M Y') . ').');
        }

        // Update ke tanggal kepulangan yang baru (Bisa maju, bisa mundur)
        $reservation->update(['departure_date' => $newDate]);

        return redirect()->back()->with('success', 'Tanggal Check-out berhasil diubah menjadi ' . $newDate->format('d M Y') . '. Tagihan akan menyesuaikan otomatis!');
    }

// 10. DAFTAR TAMU (Halaman yang lo maksud)
    public function guestIndex()
    {
        // Tarik semua data dari tabel guests
        $guests = \App\Models\Guest::latest()->get();

        // HAPUS TANDA KOMENTAR (//) DI BAWAH INI UNTUK DEBUG
        // dd($guests); 

        return view('guests.index', compact('guests'));
    }

    // 11. TOGGLE INCOGNITO
    public function toggleIncognito(Request $request, $id)
    {
        $guest = Guest::findOrFail($id);
        $guest->update([
            'is_incognito' => !$guest->is_incognito
        ]);

        return redirect()->back()->with('success', 'Status privasi tamu diperbarui.');
    }

    // 12. HAPUS TRANSAKSI
    public function destroyTransaction($id){
        $transaction = Checkout::findOrFail($id);
        $transaction->delete();
        return redirect()->back()->with('success', 'Transaksi berhasil dihapus.');
    }
}