<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE enquiries MODIFY COLUMN status
            ENUM('New','In Progress','Qualified','Converted','Not Proceeding')
            NOT NULL DEFAULT 'New'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE enquiries MODIFY COLUMN status
            ENUM('New','In Progress','Converted','Not Proceeding')
            NOT NULL DEFAULT 'New'");
    }
};
