<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('portal_feedback_items', function (Blueprint $table) {
            $table->text('dev_follow_up')->nullable()->after('client_notes');
        });
    }

    public function down(): void
    {
        Schema::table('portal_feedback_items', function (Blueprint $table) {
            $table->dropColumn('dev_follow_up');
        });
    }
};
