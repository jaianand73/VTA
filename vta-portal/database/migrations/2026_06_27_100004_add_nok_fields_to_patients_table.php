<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->string('nok_name')->nullable()->after('notes');
            $table->string('nok_email')->nullable()->after('nok_name');
            $table->string('nok_phone')->nullable()->after('nok_email');
        });
    }

    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->dropColumn(['nok_name', 'nok_email', 'nok_phone']);
        });
    }
};
