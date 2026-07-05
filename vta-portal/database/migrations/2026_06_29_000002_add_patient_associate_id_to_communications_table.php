<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('communications', function (Blueprint $table) {
            $table->unsignedBigInteger('patient_associate_id')->nullable()->after('patient_id');
            $table->foreign('patient_associate_id')->references('id')->on('patient_associates')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('communications', function (Blueprint $table) {
            $table->dropForeign(['patient_associate_id']);
            $table->dropColumn('patient_associate_id');
        });
    }
};
