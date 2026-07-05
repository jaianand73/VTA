<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->unique('patient_id');
            $table->decimal('fee_agreed_amount', 10, 2)->nullable();
            $table->string('fee_agreed_document_path')->nullable();
            $table->date('date_client_contacted')->nullable();
            $table->string('assessor')->nullable();
            $table->string('venue')->nullable();
            $table->date('assessment_date')->nullable();
            $table->decimal('assessment_cost', 10, 2)->nullable();
            $table->string('assessment_cost_document_path')->nullable();
            $table->boolean('report_sent')->default(false);
            $table->string('report_document_path')->nullable();
            $table->text('special_instructions')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assessments');
    }
};
