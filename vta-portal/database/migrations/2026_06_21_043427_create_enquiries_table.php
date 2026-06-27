<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('enquiries', function (Blueprint $table) {
            $table->id();
            $table->string('enquirer_name', 255);
            $table->string('company_name', 255)->nullable();
            $table->string('email', 255)->nullable();
            $table->string('phone', 50)->nullable();
            $table->enum('source', ['Email', 'LinkedIn', 'Phone', 'Referral Letter', 'Website', 'Word of Mouth', 'Other']);
            $table->text('reason')->nullable();
            $table->date('enquiry_date');
            $table->date('first_response_date')->nullable();
            $table->enum('status', ['New', 'In Progress', 'Converted', 'Not Proceeding'])->default('New');
            $table->unsignedBigInteger('converted_to_company_id')->nullable();
            $table->unsignedBigInteger('converted_to_case_manager_id')->nullable();
            $table->date('converted_date')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('enquiries');
    }
};
