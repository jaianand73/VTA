<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('case_manager_id');
            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->date('date_of_birth')->nullable();
            $table->string('location', 255)->nullable();
            $table->text('condition')->nullable();
            $table->enum('status', [
                'Enquiry Logged', 'Response Sent', 'Awaiting LOI', 'LOI Received',
                'Assessment Scheduled', 'Assessment Completed', 'Report Drafted', 'Report Sent',
                'Cost Estimation Sent', 'Awaiting Funding Approval', 'Funding Approved',
                'Treatment Active', 'Awaiting Further Funding', 'Discharged', 'Case Closed'
            ])->default('Enquiry Logged');
            $table->date('referral_date');
            $table->date('first_contact_date')->nullable();
            $table->date('discharge_date')->nullable();
            $table->enum('invoice_recipient_type', ['Case Manager Company', 'Solicitor', 'Insurance Company', 'Other'])->nullable();
            $table->string('invoice_recipient_name', 255)->nullable();
            $table->string('invoice_recipient_email', 255)->nullable();
            $table->text('invoice_recipient_address')->nullable();
            $table->foreignId('assigned_staff_id')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('needs_review')->default(true);
            $table->string('folder_path', 500)->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};
