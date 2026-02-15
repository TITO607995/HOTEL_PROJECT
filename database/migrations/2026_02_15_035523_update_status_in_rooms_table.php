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
    Schema::table('rooms', function (Blueprint $table) {
        // Kita tambahkan 'oo' dan 'os' ke dalam daftar pilihan enum
        $table->enum('status', ['available', 'booked', 'oo', 'os'])->change();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            //
        });
    }
};
