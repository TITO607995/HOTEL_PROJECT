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
        'place_birth',
        'payment_method',
        'reservation_type'
    ];

    public function room() 
    {
        return $this->belongsTo(Room::class);
    }
}