<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Room;
use Illuminate\Support\Facades\Storage;

class RoomController extends Controller
{
    // 1. API DAFTAR KAMAR (Katalog Mobile)
    public function index(Request $request)
    {
        $query = Room::query();

        // Fitur Pencarian berdasarkan Nomor Kamar (Bisa dipake di Flutter nanti)
        if ($request->filled('room_number')) {
            $query->where('room_number', 'like', '%' . $request->room_number . '%');
        }

        // Ambil data (Gak pakai paginate dulu biar di Flutter gampang nampil semua)
        $rooms = $query->orderBy('room_number', 'asc')->get();

        // Simulasi Foto (Placeholder)
        $simulasiFoto = [
            'https://images.unsplash.com/photo-1631049307264-da0ec9d70304?w=500',
            'https://images.unsplash.com/photo-1611892440504-42a792e24d32?w=500',
            'https://images.unsplash.com/photo-1566665797739-1674de7a421a?w=500',
            'https://images.unsplash.com/photo-1505691938895-1758d7feb511?w=500'
        ];

        // Format data sebelum dikirim ke JSON
        $rooms->transform(function ($room, $key) use ($simulasiFoto) {
            // Cek apakah ada file fisik di storage
            if ($room->image && Storage::disk('public')->exists($room->image)) {
                $room->foto_display = asset('storage/' . $room->image);
            } else {
                $room->foto_display = $simulasiFoto[$key % count($simulasiFoto)];
            }
            
            // Rapikan tulisan status
            $room->status_label = strtoupper($room->status);
            return $room;
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Berhasil mengambil data kamar',
            'data' => $rooms
        ], 200);
    }
}