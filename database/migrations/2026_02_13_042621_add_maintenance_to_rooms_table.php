<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            // Taruh di sini bro
            $table->string('maintenance_type')->nullable(); // oo atau os
            $table->text('maintenance_notes')->nullable(); // Alasan perbaikan
        });
    }

    public function down(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            // Hapus kolom jika rollback
            $table->dropColumn(['maintenance_type', 'maintenance_notes']);
        });
    }
};