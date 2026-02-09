<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $fillable = ['name'];

    // Relasi Many-to-Many ke Menu
    public function menus()
    {
        return $this->belongsToMany(Menu::class, 'role_menu');
    }
}
