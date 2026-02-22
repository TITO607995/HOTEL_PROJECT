<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('guests', function (Blueprint $table) {
            // Menambahkan kolom phone ke tabel guests
            $table->string('phone')->nullable()->after('guest_name');
        });
    }

    public function down(): void
    {
        Schema::table('guests', function (Blueprint $table) {
            // Untuk menghapus kolom jika migrasi di-rollback
            $table->dropColumn('phone');
        });
    }
};