<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up(): void
{
    Schema::table('rooms', function (Blueprint $table) {
        // Tambahkan kolom guest_id yang merujuk ke tabel guests
        $table->foreignId('guest_id')->nullable()->constrained('guests')->onDelete('set null');
    });
}

public function down(): void
{
    Schema::table('rooms', function (Blueprint $table) {
        $table->dropForeign(['guest_id']);
        $table->dropColumn('guest_id');
    });
}
};
