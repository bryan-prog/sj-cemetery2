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
        Schema::create('deceased', function (Blueprint $table) {
            $table->id();
            $table->string('name_of_deceased');
            $table->string('address_before_death');
            $table->string('sex');
            $table->date('date_of_birth');
            $table->date('date_of_death');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('deceased');
    }
};
