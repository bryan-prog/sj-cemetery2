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
        Schema::create('slots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('grave_cell_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('slot_no');
            $table->string('status')->default('available');
            $table->date('occupancy_start')->nullable();
            $table->date('occupancy_end')->nullable();
            $table->timestamps();

            $table->unique(['grave_cell_id','slot_no']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('slots');
    }
};
