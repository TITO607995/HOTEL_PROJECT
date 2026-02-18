<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Room;
use App\Models\Checkout;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReservationController extends Controller
{
    // 1. HALAMAN DAFTAR RESERVASI (TABEL)
    public function index()
    {
        $rooms = Room::all(); 

        // Ambil data reservasi terbaru beserta data kamarnya
        $reservations = Reservation::with('room')->latest()->get();

        // Kirim $rooms dan $reservations ke view
        return view('reservations.index', compact('reservations', 'rooms'));
    }

    // 2. FORM TAMBAH RESERVASI
    public function create()
    {
        // Hanya tampilkan kamar yang available agar tidak double booking
        $rooms = Room::where('status', 'available')->get();
        return view('reservations.create', compact('rooms'));
    }

    // 3. PROSES SIMPAN RESERVASI BARU
    public function store(Request $request)
    {
        // Validasi Input
        $request->validate([
            'room_id'        => 'required|exists:rooms,id',
            'guest_name'     => 'required|string|max:255',
            'arrival_date'   => 'required|date',
            'departure_date' => 'required|date|after:arrival_date',
            'email'          => 'required|email',
            'phone'          => 'required',
        ]);

        DB::transaction(function () use ($request) {
            // A. Simpan Data Reservasi
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
                'status'           => 'booked', // Status awal
            ]);

            // B. Update Status Kamar jadi 'booked'
            // Agar di dashboard warnanya jadi Biru/Booked
            Room::where('id', $request->room_id)->update(['status' => 'booked']);
        });

        return redirect()->route('reservations.index')
            ->with('success', 'Reservasi berhasil! Kamar kini berstatus Booked.');
    }

    // 4. HALAMAN CHECK-IN (REGISTRATION)
    public function registration()
    {
        $reservations = Reservation::with('room')
                        ->where(function($query) {
                            $query->where('status', 'booked')
                                  ->orWhereNull('status'); 
                        })
                        // Opsional: Jika ingin filter hanya hari ini, uncomment baris bawah ini
                        // ->whereDate('arrival_date', '<=', now()) 
                        ->latest()
                        ->get();
                        
        return view('reservations.registration', compact('reservations'));
    }
    // 5. PROSES CHECK-IN
    public function checkin($id)
    {
        DB::transaction(function () use ($id) {
            $reservation = Reservation::findOrFail($id);
            
            // Update Reservasi
            $reservation->update([
                'status' => 'checked-in',
                'reservation_type' => 'guaranteed' // Anggap tamu datang = guaranteed
            ]);

            // Update Kamar jadi 'occupied' (Merah/Terisi)
            $reservation->room->update(['status' => 'occupied']);
        });

        return redirect()->back()->with('success', 'Check-in Berhasil! Kamar sekarang Occupied.');
    }

    // 6. HALAMAN CHECK-OUT (INVOICE)
    public function checkoutPage()
    {
        // Tampilkan tamu yang sedang menginap (Checked-in / Occupied)
        $reservations = Reservation::where('status', 'checked-in')
                        ->with('room')
                        ->get();

        return view('reservations.checkout', compact('reservations'));
    }

    // 7. PROSES CHECK-OUT
    public function processCheckout(Request $request, $id)
    {
        $res = Reservation::with('room')->findOrFail($id);
        
        // Hitung Durasi & Biaya
        $checkIn = Carbon::parse($res->arrival_date);
        $checkOut = Carbon::parse($res->departure_date);
        $nights = $checkIn->diffInDays($checkOut);
        if ($nights <= 0) $nights = 1; // Minimal bayar 1 malam

        $totalAmount = ($res->room->price * $nights) + ($request->additional_charges ?? 0);

        // Simpan Data Checkout
        Checkout::create([
            'reservation_id' => $res->id,
            'additional_charges' => $request->additional_charges ?? 0,
            'notes' => $request->notes,
            'total_amount' => $totalAmount,
            'checkout_at' => now(),
        ]);

        // Update Kamar jadi 'vacant dirty' (Kuning/Kotor)
        $res->room->update(['status' => 'vacant dirty']);

        // Update Reservasi Selesai
        $res->update(['status' => 'checked-out']);

        return redirect()->route('reservations.index')
            ->with('success', 'Check-out berhasil! Kamar perlu dibersihkan.');
    }

    // 8. FUNGSI PERPANJANG MENGINAP (EXTEND)
    public function extend(Request $request, $id)
    {
        $reservation = Reservation::findOrFail($id);
        
        // Tambah hari ke tanggal pulang
        $newDate = Carbon::parse($reservation->departure_date)->addDays($request->days);
        
        $reservation->update(['departure_date' => $newDate]);

        return redirect()->back()->with('success', 'Masa menginap diperpanjang sampai ' . $newDate->format('d M Y'));
    }

    // 9. HALAMAN DAFTAR TAMU (GUEST BOOK)
    public function guestIndex()
    {
        $guests = Reservation::latest()->get();
        return view('guests.index', compact('guests'));
    }

    // 10. FITUR INCOGNITO
    public function toggleIncognito(Request $request, $id)
    {
        $reservation = Reservation::findOrFail($id);
        
        $reservation->update([
            'is_incognito' => !$reservation->is_incognito,
            'incognito_notes' => $request->notes 
        ]);

        return redirect()->back()->with('success', 'Status privasi tamu diperbarui.');
    }
}