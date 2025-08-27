<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('grave_cells', function (Blueprint $table) {
            $table->unsignedSmallInteger('max_slots')->nullable()->after('col_no');
        });


        DB::statement('
            UPDATE grave_cells
            SET max_slots = (SELECT COUNT(*) FROM slots s WHERE s.grave_cell_id = grave_cells.id)
        ');

        Schema::table('slots', function (Blueprint $table) {
            $table->unique(['grave_cell_id', 'slot_no'], 'slots_cell_slotno_unique');
        });
    }

    public function down(): void
    {
        Schema::table('slots', function (Blueprint $table) {
            $table->dropUnique('slots_cell_slotno_unique');
        });

        Schema::table('grave_cells', function (Blueprint $table) {
            $table->dropColumn('max_slots');
        });
    }
};
