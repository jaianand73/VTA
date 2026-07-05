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
        Schema::table('referral_documents', function (Blueprint $table) {
            $table->boolean('revision_requested')->default(false)->after('visible_to_associate');
            $table->text('revision_notes')->nullable()->after('revision_requested');
        });
    }

    public function down(): void
    {
        Schema::table('referral_documents', function (Blueprint $table) {
            $table->dropColumn(['revision_requested', 'revision_notes']);
        });
    }
};
