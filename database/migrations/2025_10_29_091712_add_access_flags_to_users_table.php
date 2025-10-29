<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('can_approve_deny')->default(false);
            $table->boolean('can_view_actionlogs')->default(false);
            $table->boolean('can_edit_permits')->default(false);
            $table->boolean('can_print_permits')->default(false);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'can_approve_deny',
                'can_view_actionlogs',
                'can_edit_permits',
                'can_print_permits',
            ]);
        });
    }
};
