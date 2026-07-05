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
        // Expand role enum to include GP and Family Member (Referees use this table)
        \DB::statement("ALTER TABLE enquiry_contacts MODIFY COLUMN role ENUM(
            'GP',
            'Family Member',
            'Case Manager',
            'Health Professional',
            'Line Manager',
            'Solicitor',
            'Insurer',
            'Other'
        ) NOT NULL");
    }

    public function down(): void
    {
        \DB::statement("ALTER TABLE enquiry_contacts MODIFY COLUMN role ENUM(
            'Case Manager',
            'Health Professional',
            'Line Manager',
            'Solicitor',
            'Insurer',
            'Other'
        ) NOT NULL");
    }
};
