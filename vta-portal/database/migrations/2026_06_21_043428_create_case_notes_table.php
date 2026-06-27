<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('case_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('appointment_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedBigInteger('associate_id');
            $table->date('session_date');
            $table->enum('note_type', ['Session Note', 'Progress Note', 'Discharge Note', 'Supervision Note', 'Other'])->default('Session Note');
            $table->text('content')->nullable();
            $table->string('document_path', 500)->nullable();
            $table->boolean('is_signed_off')->default(false);
            $table->foreignId('signed_off_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('signed_off_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('case_notes');
    }
};
