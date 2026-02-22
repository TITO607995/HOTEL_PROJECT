<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    protected $table = 'rooms'; 

    protected $fillable = [
        'room_number', 
        'type', 
        'price', 
        'image', 
        'status', 
        'maintenance_type', 
        'maintenance_notes'
    ];

    /**
     * Relasi ke Model Reservation
     * Ini yang bikin error tadi karena Laravel gak nemu hubungannya
     */
    public function reservations()
    {
        // Pastikan model Reservation sudah ada di folder App\Models
        return $this->hasMany(Reservation::class, 'room_id', 'id');
    }
}