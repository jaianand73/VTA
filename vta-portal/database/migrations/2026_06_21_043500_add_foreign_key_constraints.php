<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('case_managers', function (Blueprint $table) {
            $table->foreign('company_id')->references('id')->on('companies')->cascadeOnDelete();
        });

        Schema::table('patients', function (Blueprint $table) {
            $table->foreign('case_manager_id')->references('id')->on('case_managers')->cascadeOnDelete();
        });

        Schema::table('patient_associates', function (Blueprint $table) {
            $table->foreign('associate_id')->references('id')->on('associates')->cascadeOnDelete();
        });

        Schema::table('document_type_permissions', function (Blueprint $table) {
            $table->foreign('document_type_id')->references('id')->on('document_types')->cascadeOnDelete();
        });

        Schema::table('appointments', function (Blueprint $table) {
            $table->foreign('associate_id')->references('id')->on('associates')->cascadeOnDelete();
            $table->foreign('activity_type_id')->references('id')->on('activity_types');
        });

        Schema::table('case_notes', function (Blueprint $table) {
            $table->foreign('associate_id')->references('id')->on('associates')->cascadeOnDelete();
        });

        Schema::table('documents', function (Blueprint $table) {
            $table->foreign('case_manager_id')->references('id')->on('case_managers')->cascadeOnDelete();
        });

        Schema::table('communications', function (Blueprint $table) {
            $table->foreign('case_manager_id')->references('id')->on('case_managers')->cascadeOnDelete();
        });

        Schema::table('funding_cycles', function (Blueprint $table) {
            $table->foreign('cost_estimation_id')->references('id')->on('cost_estimations')->nullOnDelete();
        });

        Schema::table('associate_invoices', function (Blueprint $table) {
            $table->foreign('funding_cycle_id')->references('id')->on('funding_cycles')->nullOnDelete();
        });

        Schema::table('vta_invoices', function (Blueprint $table) {
            $table->foreign('funding_cycle_id')->references('id')->on('funding_cycles')->nullOnDelete();
        });

        Schema::table('enquiries', function (Blueprint $table) {
            $table->foreign('converted_to_company_id')->references('id')->on('companies')->nullOnDelete();
            $table->foreign('converted_to_case_manager_id')->references('id')->on('case_managers')->nullOnDelete();
        });

        Schema::table('patient_case_manager_history', function (Blueprint $table) {
            $table->foreign('patient_id')->references('id')->on('patients')->cascadeOnDelete();
            $table->foreign('previous_case_manager_id')->references('id')->on('case_managers')->nullOnDelete();
            $table->foreign('new_case_manager_id')->references('id')->on('case_managers')->cascadeOnDelete();
            $table->foreign('previous_company_id')->references('id')->on('companies')->nullOnDelete();
            $table->foreign('new_company_id')->references('id')->on('companies')->cascadeOnDelete();
        });

        Schema::table('email_intake_logs', function (Blueprint $table) {
            $table->foreign('linked_patient_id')->references('id')->on('patients')->nullOnDelete();
            $table->foreign('linked_case_manager_id')->references('id')->on('case_managers')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('email_intake_logs', function (Blueprint $table) {
            $table->dropForeign(['linked_case_manager_id']);
            $table->dropForeign(['linked_patient_id']);
        });

        Schema::table('patient_case_manager_history', function (Blueprint $table) {
            $table->dropForeign(['new_company_id']);
            $table->dropForeign(['previous_company_id']);
            $table->dropForeign(['new_case_manager_id']);
            $table->dropForeign(['previous_case_manager_id']);
            $table->dropForeign(['patient_id']);
        });

        Schema::table('enquiries', function (Blueprint $table) {
            $table->dropForeign(['converted_to_case_manager_id']);
            $table->dropForeign(['converted_to_company_id']);
        });

        Schema::table('vta_invoices', function (Blueprint $table) {
            $table->dropForeign(['funding_cycle_id']);
        });

        Schema::table('associate_invoices', function (Blueprint $table) {
            $table->dropForeign(['funding_cycle_id']);
        });

        Schema::table('funding_cycles', function (Blueprint $table) {
            $table->dropForeign(['cost_estimation_id']);
        });

        Schema::table('communications', function (Blueprint $table) {
            $table->dropForeign(['case_manager_id']);
        });

        Schema::table('documents', function (Blueprint $table) {
            $table->dropForeign(['case_manager_id']);
        });

        Schema::table('case_notes', function (Blueprint $table) {
            $table->dropForeign(['associate_id']);
        });

        Schema::table('appointments', function (Blueprint $table) {
            $table->dropForeign(['activity_type_id']);
            $table->dropForeign(['associate_id']);
        });

        Schema::table('document_type_permissions', function (Blueprint $table) {
            $table->dropForeign(['document_type_id']);
        });

        Schema::table('patient_associates', function (Blueprint $table) {
            $table->dropForeign(['associate_id']);
        });

        Schema::table('patients', function (Blueprint $table) {
            $table->dropForeign(['case_manager_id']);
        });

        Schema::table('case_managers', function (Blueprint $table) {
            $table->dropForeign(['company_id']);
        });
    }
};
