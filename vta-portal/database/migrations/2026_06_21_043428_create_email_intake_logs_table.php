<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('email_intake_logs', function (Blueprint $table) {
            $table->id();
            $table->string('from_email', 255);
            $table->string('from_name', 255)->nullable();
            $table->string('subject', 500)->nullable();
            $table->text('body')->nullable();
            $table->dateTime('received_at');
            $table->boolean('has_attachments')->default(false);
            $table->text('attachment_paths')->nullable();
            $table->boolean('processed')->default(false);
            $table->unsignedBigInteger('linked_patient_id')->nullable();
            $table->unsignedBigInteger('linked_case_manager_id')->nullable();
            $table->enum('action_taken', ['Linked to Patient', 'New Patient Created', 'Linked to Case Manager', 'Marked Irrelevant', 'Deleted'])->nullable();
            $table->foreignId('processed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('processed_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('email_intake_logs');
    }
};
