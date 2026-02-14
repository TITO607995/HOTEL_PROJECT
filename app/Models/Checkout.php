<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Checkout extends Model
{
    use HasFactory;

    protected $fillable = [
        'reservation_id',
        'additional_charges',
        'notes',
        'total_amount',
        'checkout_at'
    ];

    // Relasi balik ke reservasi agar bisa tahu nama tamunya
    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }
}