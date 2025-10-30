<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->boolean('misc_transfer_fee')->default(false)->after('funeral_service');
            $table->boolean('misc_review_dc')->default(false)->after('misc_transfer_fee');
            $table->boolean('is_indigent')->default(false)->after('misc_review_dc');
            $table->decimal('indigent_discount', 10, 2)->default(0)->after('is_indigent');
            $table->boolean('is_waived')->default(false)->after('indigent_discount');
        });
    }

    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropColumn([
                'misc_transfer_fee',
                'misc_review_dc',
                'is_indigent',
                'indigent_discount',
                'is_waived',
            ]);
        });
    }
};
