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
    Schema::create('reservations', function (Blueprint $table) {
        $table->id();
        $table->foreignId('room_id')->constrained('rooms');
        $table->string('guest_name');
        $table->integer('num_guests');
        $table->string('email'); // <--- PASTIKAN BARIS INI ADA
        $table->string('phone'); // <--- PASTIKAN BARIS INI JUGA ADA
        $table->date('arrival_date');
        $table->date('departure_date');
        $table->string('payment_method');
        $table->string('reservation_type');
        $table->string('country')->nullable();
        $table->string('city')->nullable();
        $table->string('place_birth')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
