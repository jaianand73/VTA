<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('email_intake_logs', function (Blueprint $table) {
            $table->foreignId('enquiry_id')->nullable()->after('linked_case_manager_id')
                  ->constrained('enquiries')->nullOnDelete();
            $table->foreignId('vta_invoice_id')->nullable()->after('enquiry_id')
                  ->constrained('vta_invoices')->nullOnDelete();
            $table->foreignId('funding_cycle_id')->nullable()->after('vta_invoice_id')
                  ->constrained('funding_cycles')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('email_intake_logs', function (Blueprint $table) {
            $table->dropForeign(['enquiry_id']);
            $table->dropForeign(['vta_invoice_id']);
            $table->dropForeign(['funding_cycle_id']);
            $table->dropColumn(['enquiry_id', 'vta_invoice_id', 'funding_cycle_id']);
        });
    }
};
