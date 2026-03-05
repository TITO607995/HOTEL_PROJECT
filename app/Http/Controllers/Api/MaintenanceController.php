<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Room;
use Illuminate\Http\Request;

class MaintenanceController extends Controller
{
    // 1. Ambil semua data kamar & Total OO/OS
    public function index()
    {
        $rooms = Room::orderBy('room_number', 'asc')->get();
        
        $ooCount = Room::where('status', 'oo')->count();
        $osCount = Room::where('status', 'os')->count();

        return response()->json([
            'status' => 'success',
            'data' => [
                'rooms' => $rooms,
                'oo_count' => $ooCount,
                'os_count' => $osCount
            ]
        ], 200);
    }

    // 2. Update Status Kamar
    public function update(Request $request, $id)
    {
        try {
            $room = Room::findOrFail($id);
            $room->status = $request->status;
            // Kalau di database lu ada kolom 'notes' / keterangan, bisa di-uncomment baris bawah ini:
            // $room->notes = $request->notes; 
            $room->save();

            return response()->json([
                'status' => 'success', 
                'message' => 'Status kamar ' . $room->room_number . ' berhasil diperbarui!'
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}