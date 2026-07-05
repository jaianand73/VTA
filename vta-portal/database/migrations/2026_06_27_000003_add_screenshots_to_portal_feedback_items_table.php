<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('portal_feedback_items', function (Blueprint $table) {
            $table->json('screenshots')->nullable()->after('raised_by');
        });
    }

    public function down(): void
    {
        Schema::table('portal_feedback_items', function (Blueprint $table) {
            $table->dropColumn('screenshots');
        });
    }
};
