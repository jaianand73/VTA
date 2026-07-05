<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('case_notes', function (Blueprint $table) {
            $table->text('review_feedback')->nullable()->after('needs_review');
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete()->after('review_feedback');
            $table->timestamp('reviewed_at')->nullable()->after('reviewed_by');
        });
    }

    public function down(): void
    {
        Schema::table('case_notes', function (Blueprint $table) {
            $table->dropConstrainedForeignId('reviewed_by');
            $table->dropColumn(['review_feedback', 'reviewed_at']);
        });
    }
};
