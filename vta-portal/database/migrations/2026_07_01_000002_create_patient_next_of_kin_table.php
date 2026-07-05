<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('patient_next_of_kin', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('relationship')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->timestamps();
        });

        // Drop old single NOK columns from patients
        Schema::table('patients', function (Blueprint $table) {
            $table->dropColumn(['nok_name', 'nok_email', 'nok_phone']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patient_next_of_kin');

        Schema::table('patients', function (Blueprint $table) {
            $table->string('nok_name')->nullable();
            $table->string('nok_email')->nullable();
            $table->string('nok_phone')->nullable();
        });
    }
};
