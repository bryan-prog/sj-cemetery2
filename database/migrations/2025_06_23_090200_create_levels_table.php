<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLevelsTable extends Migration
{
    public function up(): void
    {
        Schema::create('levels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('burial_site_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('level_no');
            $table->timestamps();

            $table->unique(['burial_site_id','level_no']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('levels');
    }
}
