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
     Schema::create('renewals', function (Blueprint $table) {
     $table->id();
     $table->foreignId('reservation_id')->constrained()->cascadeOnDelete();

     $table->foreignId('slot_id')->constrained('slots');
     $table->date('date_applied');
     $table->date('renewal_start');
     $table->date('renewal_end');
     $table->string('requesting_party');
     $table->string('applicant_address')->nullable();
     $table->string('contact')->nullable();
     $table->string('relationship_to_deceased')->nullable();
     $table->decimal('amount_as_per_ord', 10, 2)->nullable();
     $table->foreignId('verifiers_id')->nullable()->constrained('verifiers');
     $table->text('remarks')->nullable();
     $table->enum('status', ['pending','approved','denied', 'penalty'])->default('pending');
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
        Schema::dropIfExists('renewals');
    }
};
