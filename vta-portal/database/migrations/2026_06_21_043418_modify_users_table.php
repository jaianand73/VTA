<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'staff', 'associate', 'case_manager', 'patient'])->default('staff')->after('email');
            $table->boolean('is_active')->default(true)->after('role');
            $table->string('phone', 50)->nullable()->after('is_active');
            $table->text('notes')->nullable()->after('phone');
            $table->timestamp('last_login_at')->nullable()->after('notes');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'is_active', 'phone', 'notes', 'last_login_at']);
        });
    }
};
