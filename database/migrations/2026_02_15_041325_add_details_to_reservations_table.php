<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
        {
            Schema::table('reservations', function (Blueprint $table) {
                if (!Schema::hasColumn('reservations', 'email')) {
                    $table->string('email')->nullable()->after('guest_name');
                }
                if (!Schema::hasColumn('reservations', 'phone')) {
                    $table->string('phone')->nullable()->after('email');
                }
                if (!Schema::hasColumn('reservations', 'arrival_date')) {
                    $table->date('arrival_date')->nullable();
                }
                if (!Schema::hasColumn('reservations', 'departure_date')) {
                    $table->date('departure_date')->nullable();
                }
                if (!Schema::hasColumn('reservations', 'payment_method')) {
                    $table->string('payment_method')->default('Cash');
                }
                if (!Schema::hasColumn('reservations', 'reservation_type')) {
                    $table->string('reservation_type')->default('non-guaranteed');
                }
                if (!Schema::hasColumn('reservations', 'country')) {
                    $table->string('country')->nullable();
                }
                if (!Schema::hasColumn('reservations', 'city')) {
                    $table->string('city')->nullable();
                }
                if (!Schema::hasColumn('reservations', 'place_birth')) {
                    $table->string('place_birth')->nullable();
                }
            });
        }

        public function down(): void
        {
            Schema::table('reservations', function (Blueprint $table) {
                $table->dropColumn(['email', 'phone', 'num_guests', 'payment_method', 'reservation_type', 'country', 'city', 'place_birth']);
            });
        }
};
