<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('case_notes', function (Blueprint $table) {
            $table->string('stage')->nullable()->after('content');
            $table->boolean('needs_review')->default(false)->after('stage');
        });
    }

    public function down(): void
    {
        Schema::table('case_notes', function (Blueprint $table) {
            $table->dropColumn(['stage', 'needs_review']);
        });
    }
};
