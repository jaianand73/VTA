<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE communications MODIFY COLUMN type ENUM('Email','Phone','Letter','Meeting','WhatsApp','LinkedIn','Follow Up','Other')");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE communications MODIFY COLUMN type ENUM('Email','Phone','Letter','Meeting','WhatsApp','LinkedIn','Other')");
    }
};
