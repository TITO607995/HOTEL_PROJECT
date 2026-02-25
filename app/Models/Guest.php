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
        'phone',
        'status', // WAJIB TAMBAHKAN INI
        'guest_id', // WAJIB TAMBAHKAN INI
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
    public function room()
{
    // Change this from $this->hasOne(Room::class);
    return $this->belongsTo(Room::class);
}
    public function IsActive(){
        return $this->status === 'active'|| $this->rooms()->exits();
    }
}