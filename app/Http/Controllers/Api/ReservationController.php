<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\Guest;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReservationController extends Controller
{
    public function index()
    {
        $reservations = Reservation::with('room')
            ->where(function($query) {
                $query->where('status', '!=', 'booked')
                      ->orWhere(function($q) {
                          $q->where('status', 'booked')
                            ->whereDate('arrival_date', '>=', Carbon::today());
                      });
            })
            ->latest()->get()->map(function ($res) {
                // HITUNG DURASI MENGINAP (MINIMAL 1 MALAM)
                $checkIn = Carbon::parse($res->arrival_date);
                $checkOut = Carbon::parse($res->departure_date);
                $nights = $checkIn->diffInDays($checkOut) ?: 1;
                $hargaKamar = $res->room ? $res->room->price : 0;

                return [
                    'id' => $res->id,
                    'guest_name' => $res->guest_name,
                    'room_number' => $res->room ? $res->room->room_number : '-',
                    'room_type' => $res->room ? $res->room->type : '-',
                    'arrival' => $checkIn->format('d M Y'),
                    'departure' => $checkOut->format('d M Y'),
                    'raw_arrival' => $res->arrival_date, // Buat dikirim ke layar invoice
                    'raw_departure' => $res->departure_date,
                    'status' => strtoupper($res->status ?? 'BOOKED'),
                    'payment_method' => $res->payment_method ?? 'Cash',
                    'reservation_type' => $res->reservation_type ?? 'non-guaranteed',
                    'nights' => $nights, // <-- TAMBAHAN BARU
                    'room_price' => $hargaKamar, // <-- TAMBAHAN BARU
                    'total_price' => $nights * $hargaKamar, // <-- TAMBAHAN BARU
                ];
            });

        return response()->json(['status' => 'success', 'data' => $reservations], 200);
    }

    // 2. API SIMPAN RESERVASI BARU
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

        try {
            DB::transaction(function () use ($request) {
                // A. Simpan Reservasi
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
                    'status'           => 'booked',
                ]);

                // B. Simpan/Update Guest
                Guest::updateOrCreate(
                    ['email' => $request->email],
                    [
                        'guest_name'   => $request->guest_name,
                        'is_incognito' => false
                    ]
                );

                // C. Update status kamar jadi booked
                Room::where('id', $request->room_id)->update(['status' => 'booked']);
            });

            return response()->json(['status' => 'success', 'message' => 'Reservasi berhasil dibuat!'], 201);
            
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
    // 3. API PROSES CHECK-IN
    public function checkin(Request $request, $id)
    {
        try {
            DB::transaction(function () use ($id, $request) {
                $reservation = Reservation::with('room')->findOrFail($id);
                
                // Kalau dikirim payment_method (berarti tamu Non-Guaranteed bayar di tempat)
                if ($request->has('payment_method')) {
                    $reservation->payment_method = $request->payment_method;
                    $reservation->reservation_type = 'guaranteed'; // Ubah jadi lunas/dijamin
                }

                $reservation->update(['status' => 'checked-in']);
                
                if ($reservation->room) {
                    $reservation->room->update(['status' => 'occupied']);
                }
            });
            return response()->json(['status' => 'success', 'message' => 'Check-in & Pembayaran berhasil! Kamar sekarang Occupied.']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    // 4. API PROSES CHECK-OUT
    public function checkout(Request $request, $id)
    {
        try {
            DB::transaction(function () use ($request, $id) {
                $res = Reservation::with('room')->findOrFail($id);
                
                // Hitung malam & total harga (Default 500rb kalau harga kamar kosong)
                $nights = Carbon::parse($res->arrival_date)->diffInDays(Carbon::parse($res->departure_date)) ?: 1;
                $hargaKamar = $res->room->price ?? 500000;
                $totalAmount = ($hargaKamar * $nights) + ($request->additional_charges ?? 0);

                // Buat record Checkout
                \App\Models\Checkout::create([
                    'reservation_id' => $res->id,
                    'additional_charges' => $request->additional_charges ?? 0,
                    'notes' => $request->notes ?? 'Checkout via Mobile App',
                    'total_amount' => $totalAmount,
                    'checkout_at' => now(),
                ]);

                // Update status
                if ($res->room) {
                    $res->room->update(['status' => 'vacant dirty']); // Kamar jadi kotor
                }
                $res->update(['status' => 'checked-out']);
            });
            return response()->json(['status' => 'success', 'message' => 'Check-out berhasil! Kamar sekarang Vacant Dirty.']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    // 5. API PERPANJANG MENGINAP (EXTEND)
    public function extend(Request $request, $id)
    {
        $request->validate([
            'new_departure_date' => 'required|date',
        ]);

        try {
            $reservation = Reservation::findOrFail($id);
            $newDate = Carbon::parse($request->new_departure_date);
            $arrivalDate = Carbon::parse($reservation->arrival_date);

            if ($newDate->lte($arrivalDate)) {
                return response()->json(['status' => 'error', 'message' => 'Tanggal Check-out tidak boleh lebih awal dari Check-in!'], 400);
            }

            $reservation->update(['departure_date' => $newDate]);

            return response()->json(['status' => 'success', 'message' => 'Perpanjangan berhasil! Tanggal Check-out diubah ke ' . $newDate->format('d M Y')]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    // 6. API BATAL RESERVASI (CANCEL)
    public function cancel($id)
    {
        try {
            DB::transaction(function () use ($id) {
                $reservation = Reservation::with('room')->findOrFail($id);
                
                // Ubah status reservasi jadi cancelled
                $reservation->update(['status' => 'cancelled']);
                
                // Kembalikan kamar jadi available
                if ($reservation->room) {
                    $reservation->room->update(['status' => 'available']);
                }
            });
            return response()->json(['status' => 'success', 'message' => 'Reservasi dibatalkan! Kamar sekarang Available.']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}