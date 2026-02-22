<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guest extends Model
{
    use HasFactory;

    // Tambahkan 'status' ke dalam array $fillable agar sistem bisa mengubahnya
    protected $fillable = [
        'guest_name',
        'email',
        'is_incognito',
        'status' // WAJIB TAMBAHKAN INI
    ];

    /**
     * Boot function untuk memberikan status default saat tamu baru dibuat
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (empty($model->status)) {
                $model->status = 'active'; // Set status awal jadi active
            }
        });
    }
}