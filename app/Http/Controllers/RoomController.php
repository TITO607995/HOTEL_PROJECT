<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage; // Tambahkan ini untuk manajemen file

class RoomController extends Controller
{
    // 1. HALAMAN DAFTAR KAMAR (KATALOG)
            public function index(Request $request)
        {
            $query = Room::query();

            if ($request->filled('room_number')) {
                $query->where('room_number', 'like', '%' . $request->room_number . '%');
            }

            $rooms = $query->orderBy('room_number', 'asc')->paginate(10);

            $simulasiFoto = [
                'https://images.unsplash.com/photo-1631049307264-da0ec9d70304?w=500',
                'https://images.unsplash.com/photo-1611892440504-42a792e24d32?w=500',
                'https://images.unsplash.com/photo-1566665797739-1674de7a421a?w=500',
                'https://images.unsplash.com/photo-1505691938895-1758d7feb511?w=500'
            ];

            foreach ($rooms as $key => $room) {
                // Cek apakah file ada di folder public/images
                if ($room->image && file_exists(public_path('images/' . $room->image))) {
                    $room->foto_display = asset('images/' . $room->image);
                } else {
                    // Jika tidak ada, gunakan simulasi
                    $room->foto_display = $simulasiFoto[$key % count($simulasiFoto)];
                }
            }

            return view('rooms.index', compact('rooms'));
        }

    // 2. PROSES SIMPAN KAMAR BARU (Fungsi Baru)
public function store(Request $request)
{
    // 1. Validasi (Sering ewor karena ukuran file atau tipe file salah)
    $request->validate([
        'room_number' => 'required|unique:rooms,room_number|max:10',
        'type'        => 'required',
        'price'       => 'required|numeric',
        'image'        => 'nullable|image|mimes:jpeg,png,jpg|max:5120', // Saya naikkan ke 5MB biar gak gampang mental
    ]);

  
    
    // 2. Cek apakah file benar-benar masuk
      $imagePath = null;
    if ($request->hasFile('image')) {
        $file = $request->file('image');
        $imagePath = time() . '_' . $file->getClientOriginalName(); // Nama file unik dengan timestamp
        $file->move(public_path('images'), $imagePath); // Simpan di folder public/storage/rooms
    }

    // 3. Simpan ke Database
    // PASTIKAN kolom di DB namanya 'image' sesuai array di bawah
    Room::create([
        'room_number' => $request->room_number,
        'type'        => $request->type,
        'price'       => $request->price,
        'status'      => 'available',
        'image'       => $imagePath, // Path yang disimpan: "rooms/namafile.jpg"
    ]);

    return redirect()->back()->with('success', 'Kamar nomor ' . $request->room_number . ' berhasil ditambahkan!');
}

    // 3. HALAMAN SETTING MAINTENANCE
    public function maintenancePage(Request $request)
    {
        $statusFilter = $request->get('status', '00');
        $rooms = Room::whereIn('status', ['available', 'vacant dirty', 'oo', 'os'])
                     ->orderBy('room_number')
                     ->paginate(10);
        
        // 3. Kirim statusFilter ke view supaya tab yang aktif warnanya berubah
    return view('rooms.maintenance', [
        'rooms' => $rooms,
        'statusFilter' => $statusFilter
    ]);
    }

    // 4. PROSES UPDATE STATUS MAINTENANCE
    public function updateMaintenance(Request $request, $id)
    {
        $room = Room::findOrFail($id);
        
        if ($room->status == 'occupied' || $room->status == 'booked') {
            return redirect()->back()->with('error', 'Gagal! Kamar ' . $room->room_number . ' sedang ada tamunya.');
        }

        $notes = $request->notes;
        $type = $request->status; 

        // Jika kembali ke normal, bersihkan catatan
        if (in_array($request->status, ['available', 'vacant dirty'])) {
            $notes = null;
            $type = null;
        }

        $room->update([
            'status'            => $request->status, 
            'maintenance_type'  => $type,
            'maintenance_notes' => $notes,
        ]);

        return redirect()->back()->with('success', 'Status maintenance kamar ' . $room->room_number . ' berhasil diperbarui!');
    }
}