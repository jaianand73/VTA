<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_type_id')->constrained();
            $table->foreignId('patient_id')->nullable()->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('case_manager_id')->nullable();
            $table->foreignId('appointment_id')->nullable()->constrained()->nullOnDelete();
            $table->string('file_name', 255);
            $table->string('stored_file_name', 255);
            $table->string('file_path', 500);
            $table->unsignedBigInteger('file_size')->nullable();
            $table->string('mime_type', 100)->nullable();
            $table->boolean('is_password_protected')->default(false);
            $table->string('report_password', 255)->nullable();
            $table->date('password_shared_date')->nullable();
            $table->enum('password_shared_via', ['Email', 'WhatsApp', 'Post', 'Other'])->nullable();
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
