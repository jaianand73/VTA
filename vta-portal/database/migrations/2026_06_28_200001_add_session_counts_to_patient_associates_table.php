<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('patient_associates', function (Blueprint $table) {
            $table->integer('sessions_approved')->nullable()->after('role');
            $table->integer('sessions_used')->nullable()->after('sessions_approved');
        });
    }

    public function down(): void
    {
        Schema::table('patient_associates', function (Blueprint $table) {
            $table->dropColumn(['sessions_approved', 'sessions_used']);
        });
    }
};
