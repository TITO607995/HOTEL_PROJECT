<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void {
    Schema::create('menus', function (Blueprint $table) {
        $table->id();
        $table->string('name');       // Nama Tampilan (e.g. Data Kamar)
        $table->string('route_name'); // Nama Route (e.g. rooms.index)
        $table->string('icon')->nullable(); // Class Icon
        $table->integer('order')->default(0); // Urutan Menu
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menus');
    }
};
