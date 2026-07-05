<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('enquiries', function (Blueprint $table) {
            $table->string('client_location', 255)->nullable()->after('reason');
            $table->foreignId('nearest_associate_id')->nullable()->after('client_location')->constrained('associates')->nullOnDelete();
            $table->text('first_response_remarks')->nullable()->after('first_response_date');
        });
    }

    public function down(): void
    {
        Schema::table('enquiries', function (Blueprint $table) {
            $table->dropForeign(['nearest_associate_id']);
            $table->dropColumn(['client_location', 'nearest_associate_id', 'first_response_remarks']);
        });
    }
};
