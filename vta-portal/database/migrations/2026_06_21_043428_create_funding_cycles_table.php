<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('funding_cycles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('cost_estimation_id')->nullable();
            $table->integer('cycle_number')->default(1);
            $table->decimal('approved_amount', 10, 2);
            $table->integer('approved_sessions')->nullable();
            $table->date('approval_date');
            $table->string('approval_document_path', 500)->nullable();
            $table->string('estimated_duration', 100)->nullable();
            $table->string('funder_name', 255)->nullable();
            $table->string('funder_reference', 255)->nullable();
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('funding_cycles');
    }
};
