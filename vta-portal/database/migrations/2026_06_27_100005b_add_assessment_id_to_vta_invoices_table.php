<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vta_invoices', function (Blueprint $table) {
            $table->foreignId('assessment_id')->nullable()->after('patient_id')
                  ->constrained('assessments')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('vta_invoices', function (Blueprint $table) {
            $table->dropForeign(['assessment_id']);
            $table->dropColumn('assessment_id');
        });
    }
};
