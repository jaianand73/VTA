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
        Schema::create('referral_communications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('referral_id')->constrained('referrals')->cascadeOnDelete();
            $table->date('communication_date');
            $table->enum('type', ['Email', 'Phone', 'WhatsApp', 'Video Call', 'In-person', 'Letter', 'Other']);
            $table->enum('direction', ['Inbound', 'Outbound']);
            $table->string('subject')->nullable();
            $table->text('notes')->nullable();
            $table->string('document_path')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('referral_communications');
    }
};
