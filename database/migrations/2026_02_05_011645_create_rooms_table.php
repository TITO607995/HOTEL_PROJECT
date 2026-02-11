<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
{
    Schema::create('rooms', function (Blueprint $table) {
    $table->id();
    $table->string('room_number');
    $table->string('type'); 
    $table->integer('price');
    $table->string('image')->nullable(); // Untuk menyimpan nama file foto kamar
    $table->enum('status', ['booking', 'occupied', 'available', 'vacant dirty'])->default('available');
    $table->timestamps();
});
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};
