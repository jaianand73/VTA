<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('associate_compliance_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('associate_id')->constrained()->cascadeOnDelete();
            $table->enum('document_type', [
                'DBS Check',
                'Professional Registration',
                'Contract',
                'CSP Membership',
                'Insurance',
                'Other',
            ]);
            $table->string('document_path')->nullable();
            $table->date('expiry_date')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::table('associates', function (Blueprint $table) {
            $table->decimal('hourly_rate', 8, 2)->nullable()->after('region');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('associate_compliance_documents');
        Schema::table('associates', function (Blueprint $table) {
            $table->dropColumn('hourly_rate');
        });
    }
};
