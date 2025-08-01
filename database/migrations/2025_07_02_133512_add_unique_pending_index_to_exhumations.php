<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Create the filtered-unique index.
     *
     * SQL Server syntax:
     *   CREATE UNIQUE INDEX index_name
     *   ON table(column)
     *   WHERE <filter>;
     */
    public function up(): void
    {
        DB::statement(
            "CREATE UNIQUE INDEX ux_exhumations_from_slot_pending
             ON exhumations(from_slot_id)
             WHERE status = 'pending';"
        );
    }

    /**
     * Drop the index on rollback.
     */
    public function down(): void
    {
        DB::statement(
            "DROP INDEX ux_exhumations_from_slot_pending ON exhumations;"
        );
    }
};
