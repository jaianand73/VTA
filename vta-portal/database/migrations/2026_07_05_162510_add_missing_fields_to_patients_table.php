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
        Schema::table('patients', function (Blueprint $table) {
            if (!Schema::hasColumn('patients', 'patient_ref')) {
                $table->string('patient_ref', 50)->nullable()->unique()->after('id');
            }
            if (!Schema::hasColumn('patients', 'postcode')) {
                $table->string('postcode', 20)->nullable()->after('address');
            }
            if (!Schema::hasColumn('patients', 'address_line_1')) {
                $table->string('address_line_1', 255)->nullable()->after('address');
            }
            if (!Schema::hasColumn('patients', 'address_line_2')) {
                $table->string('address_line_2', 255)->nullable()->after('address_line_1');
            }
            if (!Schema::hasColumn('patients', 'city')) {
                $table->string('city', 100)->nullable()->after('address_line_2');
            }
            if (!Schema::hasColumn('patients', 'company_id')) {
                $table->foreignId('company_id')->nullable()->after('case_manager_id')->constrained('companies')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->dropColumn(array_filter([
                Schema::hasColumn('patients', 'patient_ref')    ? 'patient_ref'    : null,
                Schema::hasColumn('patients', 'postcode')       ? 'postcode'       : null,
                Schema::hasColumn('patients', 'address_line_1') ? 'address_line_1' : null,
                Schema::hasColumn('patients', 'address_line_2') ? 'address_line_2' : null,
                Schema::hasColumn('patients', 'city')           ? 'city'           : null,
                Schema::hasColumn('patients', 'company_id')     ? 'company_id'     : null,
            ]));
        });
    }
};
