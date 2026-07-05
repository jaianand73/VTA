<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->string('email')->nullable()->after('last_name');
            $table->string('phone')->nullable()->after('email');
            $table->text('address')->nullable()->after('phone');
            $table->decimal('fee_agreed_amount', 10, 2)->nullable()->after('first_contact_date');
            $table->string('fee_agreed_document')->nullable()->after('fee_agreed_amount');
            $table->boolean('assessment_report_sent')->default(false)->after('fee_agreed_document');
            $table->string('assessment_report_document')->nullable()->after('assessment_report_sent');
        });

        Schema::table('patient_referrers', function (Blueprint $table) {
            $table->text('special_instructions')->nullable()->after('phone');
        });
    }

    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->dropColumn(['email', 'phone', 'address', 'fee_agreed_amount', 'fee_agreed_document', 'assessment_report_sent', 'assessment_report_document']);
        });

        Schema::table('patient_referrers', function (Blueprint $table) {
            $table->dropColumn('special_instructions');
        });
    }
};
