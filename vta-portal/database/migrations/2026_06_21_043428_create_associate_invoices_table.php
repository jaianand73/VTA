<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('associate_invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('associate_id')->constrained()->cascadeOnDelete();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('funding_cycle_id')->nullable();
            $table->string('invoice_reference', 255)->nullable();
            $table->date('invoice_date');
            $table->integer('sessions_completed')->nullable();
            $table->decimal('travel_miles', 6, 2)->nullable();
            $table->decimal('session_amount', 10, 2)->nullable();
            $table->decimal('travel_amount', 10, 2)->nullable();
            $table->decimal('total_amount', 10, 2);
            $table->enum('status', ['Received', 'Verified', 'Paid', 'Disputed'])->default('Received');
            $table->date('payment_date')->nullable();
            $table->date('due_date')->nullable();
            $table->string('document_path', 500)->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('logged_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('associate_invoices');
    }
};
