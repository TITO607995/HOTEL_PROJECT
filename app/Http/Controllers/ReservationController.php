<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReservationController extends Controller
{
    // Menampilkan semua data (untuk dashboard/admin)
    public function index()
    {
        $rooms = Room::all(); 
        $reservations = Reservation::with('room')->latest()->get();
        return view('reservations.index', compact('rooms', 'reservations'));
    }

    // Form input tamu baru
    public function create()
    {
        $rooms = Room::where('status', 'available')->get();
        return view('reservations.create', compact('rooms'));
    }

    // Menyimpan data dari form ke Database
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
        ]);

        // Setelah simpan, langsung lempar ke halaman Check-in agar data langsung terlihat
        return redirect()->route('reservations.registration')->with('success', 'Reservasi berhasil dibuat!');
    }

    // Halaman Tabel Check-in yang kamu buat tadi
    public function registration()
    {
        // Ambil data terbaru agar muncul paling atas
        $reservations = Reservation::with('room')->latest()->get();
        return view('reservations.registration', compact('reservations'));
    }

public function checkin(Request $request, $id)
{
    DB::transaction(function () use ($id) {
        $reservation = \App\Models\Reservation::findOrFail($id);
        
        $reservation->room->update(['status' => 'occupied']);
        
        $reservation->update(['reservation_type' => 'guaranteed']);
    });

    return redirect()->back()->with('success', 'Tamu Berhasil Check-in!');
}

public function checkoutPage()
{
    // Ambil tamu yang status kamarnya 'occupied'
    $reservations = \App\Models\Reservation::whereHas('room', function($q) {
        $q->where('status', 'occupied');
    })->with('room')->get();

    return view('reservations.checkout', compact('reservations'));
}

public function processCheckout(Request $request, $id)
{
    $res = \App\Models\Reservation::findOrFail($id);
    
    // Hitung total (Harga Kamar + Tambahan)
    $total = $res->room->price + ($request->additional_charges ?? 0);

    \App\Models\Checkout::create([
        'reservation_id' => $res->id,
        'additional_charges' => $request->additional_charges ?? 0,
        'notes' => $request->notes,
        'total_amount' => $total,
        'checkout_at' => now(),
    ]);

    // Update status kamar jadi 'vacant dirty' (perlu dibersihkan) setelah check-out
    $res->room->update(['status' => 'vacant dirty']);

    return redirect()->route('reservations.index')->with('success', 'Check-out berhasil untuk ' . $res->guest_name);
}
}