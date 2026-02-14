<?php

namespace App\Models; // Pastikan tidak ada typo di sini

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;
    protected $fillable = ['room_number', 'type', 'price', 'status'];

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }
}