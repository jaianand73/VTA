<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('patient_case_manager_history', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('patient_id');
            $table->unsignedBigInteger('previous_case_manager_id')->nullable();
            $table->unsignedBigInteger('new_case_manager_id');
            $table->unsignedBigInteger('previous_company_id')->nullable();
            $table->unsignedBigInteger('new_company_id');
            $table->date('change_date');
            $table->text('reason')->nullable();
            $table->foreignId('changed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patient_case_manager_history');
    }
};
