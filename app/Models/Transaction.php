<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'reservation_id',
        'checkout_at',
        'total_amount',
        'additional_charges',
        'notes'
    ];

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }
}