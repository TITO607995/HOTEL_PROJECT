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
    // Menampilkan semua data (untuk dashboard/admin)
  public function index()
{
    // 1. Ambil data semua kamar
    $rooms = Room::all(); 

    // 2. Ambil data reservasi yang aktif (check-in)
    $reservations = Reservation::with('room')
                    ->where('status', 'check-in') 
                    ->latest()
                    ->get();

    // 3. MAPPING: Ubah $reservations menjadi $roomList agar sesuai dengan Blade kamu
    $roomList = $reservations->map(function ($res) {
        return [
            'no'           => $res->room->room_number ?? '-',
            'type'         => $res->room->room_type ?? 'Standard',
            'left_status'  => 'Terisi', 
            'payment'      => $res->guest_name, // Menampilkan nama tamu di kolom payment
            'is_paid'      => $res->payment_status === 'paid', 
            'action'       => 'Checked In',
            'action_color' => 'bg-green-500',
            'visibility'   => $res->created_at->format('H:i'),
        ];
    });

    // 4. Statistik (Key-nya disesuaikan agar rapi di tampilan)
    $stats = [
        'Occupied'  => Room::where('status', 'occupied')->count(),
        'Dirty'     => Room::where('status', 'vacant dirty')->count(),
        'Available' => Room::where('status', 'available')->count(),
    ];

    // Kirim 'roomList' (bukan 'reservations') agar @forelse($roomList) di HTML tidak error
    return view('reservations.index', compact('rooms', 'roomList', 'stats'));
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

        return redirect()->route('reservations.registration')->with('success', 'Reservasi berhasil dibuat!');
    }

    // Halaman Tabel Check-in
    public function registration()
    {
        $reservations = Reservation::with('room')->latest()->get();
        return view('reservations.registration', compact('reservations'));
    }

    public function checkin(Request $request, $id)
    {
        DB::transaction(function () use ($id) {
            $reservation = Reservation::findOrFail($id);
            $reservation->room->update(['status' => 'occupied']);
            $reservation->update(['reservation_type' => 'guaranteed']);
        });

        return redirect()->back()->with('success', 'Tamu Berhasil Check-in!');
    }

    public function checkoutPage()
    {
        $reservations = Reservation::whereHas('room', function($q) {
            $q->where('status', 'occupied');
        })->with('room')->get();

        return view('reservations.checkout', compact('reservations'));
    }

public function processCheckout(Request $request, $id)
{
    $res = Reservation::with('room')->findOrFail($id);
    
    $checkIn = \Carbon\Carbon::parse($res->arrival_date);
    $checkOut = \Carbon\Carbon::parse($res->departure_date);
    $nights = $checkIn->diffInDays($checkOut);
    if ($nights <= 0) $nights = 1; 

    $roomPriceTotal = $res->room->price * $nights;
    $totalAmount = $roomPriceTotal + ($request->additional_charges ?? 0);

    Checkout::create([
        'reservation_id' => $res->id,
        'additional_charges' => $request->additional_charges ?? 0,
        'notes' => $request->notes,
        'total_amount' => $totalAmount,
        'checkout_at' => now(),
    ]);

    $res->room->update(['status' => 'vacant dirty']);

    $res->update(['status' => 'checked-out']);

    return redirect()->route('reservations.index')
        ->with('success', 'Check-out berhasil! Kamar ' . $res->room->room_number . ' kini berstatus Vacant Dirty.');
}

    // FUNGSI EXTEND (HANYA SATU SAJA)
    public function extend(Request $request, $id)
    {
        $reservation = Reservation::findOrFail($id);
        $additionalDays = $request->days; 
        
        $newDate = Carbon::parse($reservation->departure_date)->addDays($additionalDays);
        
        $reservation->update([
            'departure_date' => $newDate
        ]);

        return redirect()->back()->with('success', 'Waktu menginap ' . $reservation->guest_name . ' berhasil diperpanjang sampai ' . $newDate->format('d M Y'));
    }

    public function guestIndex()
    {
        $guests = Reservation::latest()->get();
        return view('guests.index', compact('guests'));
    }

    public function toggleIncognito(Request $request, $id)
    {
        $reservation = Reservation::findOrFail($id);
        
        $reservation->update([
            'is_incognito' => !$reservation->is_incognito,
            'incognito_notes' => $request->notes 
        ]);

        return redirect()->back()->with('success', 'Status privasi dan catatan berhasil diperbarui!');
    }
}