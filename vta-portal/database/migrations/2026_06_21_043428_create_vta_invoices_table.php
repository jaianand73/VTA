<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vta_invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('funding_cycle_id')->nullable();
            $table->string('invoice_number', 100)->unique();
            $table->date('invoice_date');
            $table->date('due_date')->nullable();
            $table->enum('recipient_type', ['Case Manager Company', 'Solicitor', 'Insurance Company', 'Other']);
            $table->string('recipient_name', 255);
            $table->string('recipient_email', 255)->nullable();
            $table->text('recipient_address')->nullable();
            $table->integer('sessions_invoiced')->nullable();
            $table->decimal('session_amount', 10, 2)->nullable();
            $table->decimal('additional_charges', 10, 2)->nullable();
            $table->decimal('total_amount', 10, 2);
            $table->enum('status', ['Draft', 'Sent', 'Paid', 'Overdue', 'Cancelled'])->default('Draft');
            $table->date('payment_date')->nullable();
            $table->string('document_path', 500)->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vta_invoices');
    }
};
