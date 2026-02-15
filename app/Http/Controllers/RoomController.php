<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    public function index(Request $request)
    {
        $query = Room::query();

        // SEARCH NOMOR KAMAR
        if ($request->room_number) {
            $query->where('room_number', 'like', '%' . $request->room_number . '%');
        }

        $rooms = $query->get();

        // SIMULASI FOTO JIKA TIDAK ADA IMAGE
        $simulasiFoto = [
            'https://images.unsplash.com/photo-1631049307264-da0ec9d70304?w=500',
            'https://images.unsplash.com/photo-1611892440504-42a792e24d32?w=500',
            'https://images.unsplash.com/photo-1566665797739-1674de7a421a?w=500',
            'https://images.unsplash.com/photo-1505691938895-1758d7feb511?w=500'
        ];

        foreach ($rooms as $key => $room) {
            if ($room->image) {
                $room->foto_display = asset('storage/rooms/' . $room->image);
            } else {
                $room->foto_display = $simulasiFoto[$key % count($simulasiFoto)];
            }
        }

        return view('rooms.index', compact('rooms'));
    }

    public function maintenancePage()
{
    // Kita ambil semua kamar agar bisa di-setting satu per satu
    $rooms = \App\Models\Room::all();
    return view('rooms.maintenance', compact('rooms'));
}

public function updateMaintenance(Request $request, $id)
{
    $room = \App\Models\Room::findOrFail($id);
    
    // Pastikan status yang dikirim adalah 'available', 'booked', 'oo', atau 'os'
    $room->update([
        'status' => $request->status,
        'maintenance_type' => $request->status == 'available' ? null : $request->status,
        'maintenance_notes' => $request->status == 'available' ? null : $request->notes,
    ]);

    return redirect()->back()->with('success', 'Status kamar ' . $room->room_number . ' berhasil diperbarui!');
}
}
