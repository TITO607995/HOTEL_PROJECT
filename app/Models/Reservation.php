<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_id', 
        'guest_name', 
        'num_guests', 
        'email', 
        'phone', 
        'arrival_date', 
        'departure_date',
        'country',
        'city',
        'status',
        'place_birth',
        'payment_method',
        'reservation_type',
        'flight_detail',
        'pickup_service',
        'remarks',
        'identity_number', // Tambahan: KTP/Passport
        'reservation_code', // Tambahan: Kode Unik Reservasi
    ];

   public function room()
{
    return $this->belongsTo(Room::class, 'room_id');
}
}