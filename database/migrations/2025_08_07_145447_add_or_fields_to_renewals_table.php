<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('renewals', function (Blueprint $table) {

            $table->string('or_number', 50)
                  ->nullable()
                  ->after('remarks');

            $table->date('or_issued_at')
                  ->nullable()
                  ->after('or_number');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
         Schema::table('renewals', function (Blueprint $table) {
            $table->dropColumn(['or_number', 'or_issued_at']);
        });
    }
};
