<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('exhumations', function (Blueprint $table) {
            if (!Schema::hasColumn('exhumations', 'or_number')) {
                $table->string('or_number')->nullable();
            }
            if (!Schema::hasColumn('exhumations', 'or_issued_at')) {
                $table->date('or_issued_at')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('exhumations', function (Blueprint $table) {
            if (Schema::hasColumn('exhumations', 'or_issued_at')) {
                $table->dropColumn('or_issued_at');
            }
            if (Schema::hasColumn('exhumations', 'or_number')) {
                $table->dropColumn('or_number');
            }
        });
    }
};
