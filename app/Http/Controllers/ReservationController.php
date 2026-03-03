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
    // 1. HALAMAN DAFTAR SEMUA RESERVASI
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

    // 3. PROSES SIMPAN RESERVASI BARU
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
            // Simpan data Reservasi - Status default 'booked' agar muncul di antrean registrasi
            Reservation::create([
                'room_id'          => $request->room_id,
                'guest_name'       => $request->guest_name,
                'identity_number'  => $request->identity_number,
                'num_guests'       => $request->num_guests ?? 1,
                'email'            => $request->email,
                'phone'            => $request->phone,
                'arrival_date'     => $request->arrival_date,
                'departure_date'   => $request->departure_date,
                'payment_method'   => $request->payment_method ?? 'Cash',
                'reservation_type' => $request->reservation_type ?? 'non-guaranteed',
                'flight_detail'    => $request->flight_detail,
                'pickup_service'   => $request->pickup_service ?? 'No',
                'remarks'          => $request->remarks,
                'country'          => $request->country,
                'city'             => $request->city,
                'place_birth'      => $request->place_birth,
                'status'           => 'booked', 
            ]);

            // Update atau buat data tamu di master Guest
            Guest::updateOrCreate(
                ['email' => $request->email],
                [
                    'guest_name'   => $request->guest_name,
                    'is_incognito' => false
                ]
            );

            // Set kamar jadi booked (agar tidak bisa dipesan orang lain)
            Room::where('id', $request->room_id)->update(['status' => 'booked']);
        });

        return redirect()->route('reservations.registration')->with('success', 'Tamu baru berhasil didaftarkan ke antrean!');
    }

    // 4. HALAMAN REGISTRASI (ANTREAN KEDATANGAN)
    public function registration()
    {
        // LOGIKA: Hanya tampilkan tamu yang BELUM masuk kamar (booked atau check-in)
        // Tamu yang sudah 'checked-in' akan hilang dari sini agar daftar tidak penuh.
        $reservations = Reservation::with('room')
            ->whereIn('status', ['booked', 'check-in'])
            ->orWhereNull('status')
            ->latest()
            ->get();
            
        return view('reservations.registration', compact('reservations'));
    }

    // 5. PROSES CHECK-IN (AKTIVASI KAMAR)
    public function checkin($id)
    {
        DB::transaction(function () use ($id) {
            $reservation = Reservation::findOrFail($id);

            // Update status reservasi jadi 'checked-in' (Otomatis hilang dari antrean)
            $reservation->update(['status' => 'checked-in']);

            // Update status kamar jadi 'occupied'
            Room::where('id', $reservation->room_id)->update(['status' => 'occupied']);
        });

        return redirect()->back()->with('success', 'Kunci diaktifkan! Tamu telah masuk kamar.');
    }

    // 6. HALAMAN DAFTAR CHECK-OUT
    public function checkoutPage()
    {
        // Menampilkan tamu yang sedang berada di dalam hotel (In-House)
        $reservations = Reservation::where('status', 'checked-in')->with('room')->get();
        return view('reservations.checkout', compact('reservations'));
    }

    // 7. HALAMAN DETAIL TAGIHAN
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

    // 8. FINALISASI CHECK-OUT
    public function processCheckout(Request $request, $id)
    {
        $res = Reservation::with('room')->findOrFail($id);
        $nights = Carbon::parse($res->arrival_date)->diffInDays(Carbon::parse($res->departure_date)) ?: 1;
        $totalAmount = ($res->room->price * $nights) + ($request->additional_charges ?? 0);

        DB::transaction(function () use ($res, $request, $totalAmount) {
            Checkout::create([
                'reservation_id'     => $res->id,
                'additional_charges' => $request->additional_charges ?? 0,
                'notes'              => $request->notes,
                'total_amount'       => $totalAmount,
                'checkout_at'        => now(),
            ]);

            $res->room->update(['status' => 'vacant dirty']);
            $res->update(['status' => 'checked-out']);
        });

        return redirect()->route('reservations.index')->with('success', 'Tamu berhasil check-out.');
    }

    // 9. ARCHIVE (PINDAH KE HISTORY)
    public function archive($id)
    {
        $reservation = Reservation::findOrFail($id);
        $reservation->update(['status' => 'archived']);

        return redirect()->route('reservations.registration')->with('success', 'Data berhasil diarsipkan.');
    }

    // --- Sisanya fungsi Master Guest & Extend tetap sama ---
    public function extend(Request $request, $id) {
        $reservation = Reservation::findOrFail($id);
        $reservation->update(['departure_date' => $request->new_departure_date]);
        return redirect()->back()->with('success', 'Masa inap diperpanjang.');
    }

    public function guestIndex() {
        $guests = Guest::latest()->get();
        return view('guests.index', compact('guests'));
    }

    public function toggleIncognito($id) {
        $guest = Guest::findOrFail($id);
        $guest->update(['is_incognito' => !$guest->is_incognito]);
        return redirect()->back();
    }
    public function history(){
        $reservations = Reservation::with('room')
        ->whereIn('status', ['checked-out', 'archived'])
        ->latest()
        ->get();
        return view('reservations.history', compact('reservations'));
    }
    public function moveToHistory($id)
        {
            $reservation = Reservation::findOrFail($id);
            
            // Contoh logika: Ubah status menjadi 'completed' atau 'archived'
            $reservation->update(['status' => 'history']); 

            return redirect()->route('reservations.index')->with('success', 'Data berhasil dipindahkan ke history');
        }
}