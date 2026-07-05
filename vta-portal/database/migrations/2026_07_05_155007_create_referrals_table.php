<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('referrals', function (Blueprint $table) {
            $table->id();
            $table->string('referral_ref', 50)->nullable()->unique();
            $table->foreignId('enquiry_id')->nullable()->constrained()->nullOnDelete();

            // Patient identity
            $table->string('patient_first_name', 100);
            $table->string('patient_last_name', 100);
            $table->date('patient_dob')->nullable();
            $table->string('patient_address', 500)->nullable();
            $table->string('patient_postcode', 20)->nullable();
            $table->string('patient_phone', 50)->nullable();
            $table->string('patient_email', 255)->nullable();

            // Case management
            $table->foreignId('company_id')->nullable()->constrained('companies')->nullOnDelete();
            $table->foreignId('case_manager_id')->nullable()->constrained('case_managers')->nullOnDelete();

            // Special instructions
            $table->text('special_instructions')->nullable();

            // Go-ahead to Visit
            $table->date('visit_approved_date')->nullable();
            $table->string('visit_approved_document', 500)->nullable();

            // Associate mapping
            $table->foreignId('associate_id')->nullable()->constrained('associates')->nullOnDelete();

            // Proposal
            $table->date('proposal_submitted_date')->nullable();
            $table->string('proposal_document', 500)->nullable();
            $table->date('proposal_approved_date')->nullable();

            // Status & meta
            $table->enum('status', [
                'New',
                'In Progress',
                'Awaiting Go-ahead',
                'Assessment',
                'Proposal Submitted',
                'Approved',
                'Not Proceeding',
            ])->default('New');

            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('referrals');
    }
};
