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
        // MASUKKAN KODEMU DI SINI
        Schema::create('checkouts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reservation_id')->constrained('reservations');
            $table->integer('additional_charges')->default(0); // Untuk denda/tambahan
            $table->text('notes')->nullable(); // Catatan barang rusak/hilang
            $table->integer('total_amount');
            $table->timestamp('checkout_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('checkouts');
    }
};