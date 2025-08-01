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
      Schema::create('reservations', function (Blueprint $table) {

    $table->id();
    $table->foreignId('level_id')->nullable()->constrained('levels')->onDelete('no action');
    $table->foreignId('deceased_id')->nullable()->constrained('deceased')->nullOnDelete();
    $table->foreignId('burial_site_id')->nullable()->constrained('burial_sites')->onDelete('no action');
    $table->foreignId('grave_diggers_id')->nullable()->constrained('grave_diggers')->nullOnDelete();
    $table->foreignId('verifiers_id')->nullable()->constrained('verifiers')->nullOnDelete();
    $table->foreignId('slot_id')->nullable()->constrained('slots')->cascadeOnDelete();

    $table->date('date_applied');
    $table->string('applicant_name');
    $table->string('applicant_address')->nullable();
    $table->string('applicant_contact_no')->nullable();
    $table->string('relationship_to_deceased')->nullable();
    $table->string('amount_as_per_ord')->nullable();
    $table->string('funeral_service')->nullable();
    $table->text('other_info')->nullable();
    $table->dateTime('internment_sched');

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
        Schema::dropIfExists('reservations');
    }
};
