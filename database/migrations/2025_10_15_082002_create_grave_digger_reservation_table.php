<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('grave_digger_reservation', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reservation_id')
                  ->constrained('reservations')
                  ->cascadeOnDelete();

            $table->foreignId('grave_digger_id')
                  ->constrained('grave_diggers')
                  ->cascadeOnDelete();

            $table->timestamps();

            $table->unique(['reservation_id', 'grave_digger_id'], 'gdr_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grave_digger_reservation');
    }
};
