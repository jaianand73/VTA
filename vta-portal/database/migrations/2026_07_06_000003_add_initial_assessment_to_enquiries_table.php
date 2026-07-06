<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('enquiries', function (Blueprint $table) {
            $table->boolean('initial_assessment_approved')->nullable()->after('notes');
            $table->text('initial_assessment_reason')->nullable()->after('initial_assessment_approved');
        });
    }

    public function down(): void
    {
        Schema::table('enquiries', function (Blueprint $table) {
            $table->dropColumn(['initial_assessment_approved', 'initial_assessment_reason']);
        });
    }
};
