<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('portal_feedback_items', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['change', 'question', 'improvement', 'bug']);
            $table->string('section', 10)->nullable();
            $table->string('reference', 10)->nullable();
            $table->enum('priority', ['critical', 'high', 'medium', 'low', 'new'])->default('medium');
            $table->string('title', 500);
            $table->text('description');
            $table->text('dev_context')->nullable();

            // Samy's response fields
            $table->enum('samy_status', ['pending', 'approved', 'hold', 'rejected'])->default('pending');
            $table->text('samy_response')->nullable();
            $table->timestamp('samy_responded_at')->nullable();

            // Dev tracking
            $table->enum('dev_status', ['not_started', 'in_progress', 'done'])->default('not_started');
            $table->text('dev_notes')->nullable();

            // Bug-specific
            $table->enum('severity', ['critical', 'high', 'medium', 'low'])->nullable();
            $table->string('raised_by', 255)->nullable();

            $table->boolean('is_seeded')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('portal_feedback_items');
    }
};
