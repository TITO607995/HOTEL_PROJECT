<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Guest extends Model
{
    // Ini penting agar kolom ini bisa diisi secara otomatis
    protected $fillable = ['guest_name', 'email', 'is_incognito'];
}