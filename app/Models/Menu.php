<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $fillable = ['name', 'route_name', 'icon', 'order'];

    // Relasi ke Role (Many to Many)
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_menu');
    }
}