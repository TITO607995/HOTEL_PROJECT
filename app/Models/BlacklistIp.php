<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlacklistIp extends Model
{
    // Tambahkan baris ini supaya Laravel izinin input datanya
    protected $fillable = [
        'ip_address',
        'reason',
    ];
}