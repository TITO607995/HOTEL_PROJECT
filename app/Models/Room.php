<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    // Field yang boleh diisi
    protected $fillable = ['room_number', 'type', 'price', 'status'];
}