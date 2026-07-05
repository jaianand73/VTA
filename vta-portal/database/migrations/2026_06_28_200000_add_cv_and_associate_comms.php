<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // CV path on associates
        Schema::table('associates', function (Blueprint $table) {
            $table->string('cv_path')->nullable()->after('notes');
        });

        // Associate FK on communications so comms can be logged against an associate
        Schema::table('communications', function (Blueprint $table) {
            $table->foreignId('associate_id')->nullable()->after('patient_id')
                  ->constrained('associates')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('communications', function (Blueprint $table) {
            $table->dropForeign(['associate_id']);
            $table->dropColumn('associate_id');
        });

        Schema::table('associates', function (Blueprint $table) {
            $table->dropColumn('cv_path');
        });
    }
};
