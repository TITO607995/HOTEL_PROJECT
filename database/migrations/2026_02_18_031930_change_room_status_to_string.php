<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            // Ubah tipe kolom status jadi string (VARCHAR 255)
            // Agar bisa menampung 'occupied', 'booked', 'vacant dirty', dll.
            $table->string('status')->change();
        });
    }

    public function down(): void
    {
        // Tidak perlu diisi untuk kasus ini
    }
};