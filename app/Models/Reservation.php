<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;

    // Field yang boleh diisi (Mass Assignment)
    // Sesuai dengan form di UI: Nama, Jumlah Tamu, Email, HP
    protected $fillable = [
        'room_id', 
        'guest_name', 
        'num_guests', 
        'guest_email', 
        'guest_phone', 
        'check_in', 
        'check_out', 
        'total_price'
    ];

    /**
     * Relasi ke Model Room
     * Satu reservasi pasti punya satu kamar
     */
    public function room()
    {
        return $this->belongsTo(Room::class);
    }
}