<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('exhumations', function (Blueprint $table) {
            $table->boolean('for_cremation')->default(false);
        });
    }

    public function down(): void
    {
        Schema::table('exhumations', function (Blueprint $table) {
            $table->dropColumn('for_cremation');

        });
    }
};
