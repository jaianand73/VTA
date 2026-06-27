<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * The communications.summary column was created NOT NULL, but the
     * controller validation has always treated it as optional ('nullable|string').
     * Leaving the Summary field blank on any "Log Communication" form
     * (Enquiry, Patient, or Case Manager page) throws a SQL integrity
     * violation instead of saving. Same latent issue for communication_date,
     * which is also NOT NULL at the DB level but nullable in validation —
     * the form always supplies a default value via JS so it's lower risk,
     * but fixed here too for consistency/defense in depth.
     *
     * Uses raw SQL (not Schema::table()->change()) because this project
     * does not have doctrine/dbal installed, which Laravel's column-modify
     * helper requires.
     */
    public function up(): void
    {
        DB::statement('ALTER TABLE communications MODIFY summary TEXT NULL');
        DB::statement('ALTER TABLE communications MODIFY communication_date DATETIME NULL');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE communications MODIFY summary TEXT NOT NULL');
        DB::statement('ALTER TABLE communications MODIFY communication_date DATETIME NOT NULL');
    }
};
