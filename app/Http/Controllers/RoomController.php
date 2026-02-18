<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    // 1. HALAMAN DAFTAR KAMAR (KATALOG UNTUK TAMU/RESEPSIONIS)
    public function index(Request $request)
    {
        $query = Room::query();

        // Fitur Pencarian berdasarkan Nomor Kamar
        if ($request->room_number) {
            $query->where('room_number', 'like', '%' . $request->room_number . '%');
        }

        $rooms = $query->get();

        // Simulasi Foto (Placeholder agar tampilan cantik)
        $simulasiFoto = [
            'https://images.unsplash.com/photo-1631049307264-da0ec9d70304?w=500',
            'https://images.unsplash.com/photo-1611892440504-42a792e24d32?w=500',
            'https://images.unsplash.com/photo-1566665797739-1674de7a421a?w=500',
            'https://images.unsplash.com/photo-1505691938895-1758d7feb511?w=500'
        ];

        foreach ($rooms as $key => $room) {
            // Jika ada foto di database pakai itu, jika tidak pakai foto simulasi
            if ($room->image) {
                $room->foto_display = asset('storage/rooms/' . $room->image);
            } else {
                $room->foto_display = $simulasiFoto[$key % count($simulasiFoto)];
            }
        }

        return view('rooms.index', compact('rooms'));
    }

    // 2. HALAMAN SETTING OO/OS (MAINTENANCE)
    public function maintenancePage()
    {
        // FILTER KEAMANAN:
        // Hanya tampilkan kamar yang: Available, Dirty, atau sedang Rusak (OO/OS).
        // Kamar 'Occupied' (Terisi) & 'Booked' DISEMBUNYIKAN agar tidak salah blokir.
        $rooms = Room::whereIn('status', ['available', 'vacant dirty', 'oo', 'os'])
                     ->orderBy('room_number')
                     ->get();
        
        return view('rooms.maintenance', compact('rooms'));
    }

    // 3. PROSES UPDATE STATUS OO/OS
    public function updateMaintenance(Request $request, $id)
    {
        $room = Room::findOrFail($id);
        
        // VALIDASI BACKEND:
        // Pastikan kamar tidak sedang terisi tamu saat tombol ditekan
        if ($room->status == 'occupied' || $room->status == 'booked') {
            return redirect()->back()->with('error', 'Gagal! Kamar ' . $room->room_number . ' sedang ada tamunya.');
        }

        // LOGIKA RESET:
        // Jika status diubah jadi 'available', hapus catatan kerusakannya
        $notes = $request->notes;
        $type = $request->status; // oo atau os

        if ($request->status == 'available') {
            $notes = null;
            $type = null;
        } elseif ($request->status == 'vacant dirty') {
             // Jika diset dirty (siap dibersihkan), anggap maintenance selesai
             $notes = null;
             $type = null;
        }

        // Update Database
        $room->update([
            'status' => $request->status, 
            'maintenance_type' => $type,
            'maintenance_notes' => $notes,
        ]);

        return redirect()->back()->with('success', 'Status maintenance kamar ' . $room->room_number . ' berhasil diperbarui!');
    }
}