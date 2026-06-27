<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cost_estimations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->integer('version_number')->default(1);
            $table->string('title', 255)->nullable();
            $table->decimal('estimated_amount', 10, 2);
            $table->integer('estimated_sessions')->nullable();
            $table->string('estimated_duration', 100)->nullable();
            $table->date('sent_date')->nullable();
            $table->string('sent_to', 255)->nullable();
            $table->text('notes')->nullable();
            $table->string('document_path', 500)->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cost_estimations');
    }
};
