<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('uat_test_results', function (Blueprint $table) {
            $table->id();
            $table->string('step_reference', 10);       // A1, A2, B3 …
            $table->string('step_title', 255);
            $table->enum('result', ['pass', 'fail', 'pass_with_improvement']);
            $table->text('comment')->nullable();         // required for fail, optional for improvement
            $table->string('tested_by', 100);
            $table->timestamp('tested_at');
            $table->unsignedBigInteger('feedback_item_id')->nullable(); // linked PortalFeedbackItem if created
            $table->foreign('feedback_item_id')->references('id')->on('portal_feedback_items')->nullOnDelete();
            $table->timestamps();

            $table->unique('step_reference');            // one result per step (upsert on re-test)
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('uat_test_results');
    }
};
