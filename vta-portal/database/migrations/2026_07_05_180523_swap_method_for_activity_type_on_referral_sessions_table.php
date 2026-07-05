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
        Schema::table('referral_sessions', function (Blueprint $table) {
            $table->unsignedBigInteger('activity_type_id')->nullable()->after('session_date');
            $table->foreign('activity_type_id')->references('id')->on('activity_types')->nullOnDelete();
            $table->string('scheduled_at', 50)->nullable()->after('activity_type_id');
            $table->integer('duration_minutes')->nullable()->after('scheduled_at');
            $table->string('location')->nullable()->after('duration_minutes');
            // keep method nullable for any existing rows, then drop
            $table->string('method')->nullable()->change();
        });

        // Copy method value into notes prefix where set (safety net for any existing data)
        \DB::statement("UPDATE referral_sessions SET notes = CONCAT('[', method, '] ', COALESCE(notes, '')) WHERE method IS NOT NULL AND method != ''");

        Schema::table('referral_sessions', function (Blueprint $table) {
            $table->dropColumn('method');
        });
    }

    public function down(): void
    {
        Schema::table('referral_sessions', function (Blueprint $table) {
            $table->enum('method', ['In-person','Video','WhatsApp','Phone','Activity-based','Other'])->nullable()->after('session_date');
            $table->dropForeign(['activity_type_id']);
            $table->dropColumn(['activity_type_id', 'scheduled_at', 'duration_minutes', 'location']);
        });
    }
};
