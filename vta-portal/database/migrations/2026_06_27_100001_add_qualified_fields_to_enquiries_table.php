<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('enquiries', function (Blueprint $table) {
            $table->boolean('qualified_as_referral')->default(false)->after('status');
            $table->date('qualified_date')->nullable()->after('qualified_as_referral');
            $table->text('qualified_remarks')->nullable()->after('qualified_date');
        });
    }

    public function down(): void
    {
        Schema::table('enquiries', function (Blueprint $table) {
            $table->dropColumn(['qualified_as_referral', 'qualified_date', 'qualified_remarks']);
        });
    }
};
