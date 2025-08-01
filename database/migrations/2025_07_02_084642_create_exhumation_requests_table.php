<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exhumations', function (Blueprint $t) {
            $t->id();
            $t->foreignId('reservation_id')->constrained()->cascadeOnDelete();
            $t->foreignId('from_slot_id') ->constrained('slots')->onDelete('no action');
            $t->foreignId('to_slot_id')->nullable()->constrained('slots') ->onDelete('no action');
            $t->date   ('date_applied');
            $t->string ('requesting_party');
            $t->string ('relationship_to_deceased')->nullable();
            $t->string('contact')->nullable();
            $t->string('current_location')->nullable();
            $t->string('address')->nullable();
            $t->string('amount_as_per_ord')->nullable();
            $t->foreignId('verifiers_id')->nullable()->constrained('verifiers')->onDelete('no action');

            $t->enum('status', ['pending','approved','denied'])
               ->default('pending');
            $t->text('remarks')->nullable();

            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exhumations');
    }
};
