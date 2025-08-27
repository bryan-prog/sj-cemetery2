<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('reservations', function (Blueprint $table) {
            $table->foreignId('family_id')
                  ->nullable()
                  ->constrained()
                  ->nullOnDelete()
                  ->after('verifiers_id');
        });

        Schema::table('grave_cells', function (Blueprint $table) {
            $table->foreignId('family_id')
                  ->nullable()
                  ->constrained()
                  ->nullOnDelete()
                  ->after('col_no');
        });
    }

    public function down(): void {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropConstrainedForeignId('family_id');
        });
        Schema::table('grave_cells', function (Blueprint $table) {
            $table->dropConstrainedForeignId('family_id');
        });
    }
};

