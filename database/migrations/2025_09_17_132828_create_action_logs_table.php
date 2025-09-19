<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('action_logs', function (Blueprint $table) {
            $table->id();


            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('username')->nullable()->index();


            $table->string('action');
            $table->string('target_type');
            $table->unsignedBigInteger('target_id')->index();


            $table->timestamp('happened_at')->nullable()->index();


            $table->json('details')->nullable();

            $table->timestamps();


            $table->foreign('user_id')
                  ->references('id')->on('users')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('action_logs');
    }
};
