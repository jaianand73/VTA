<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE patients MODIFY COLUMN status ENUM(
            'Enquiry Logged','Response Sent','Awaiting LOI','LOI Received',
            'Assessment Scheduled','Assessment Completed','Report Drafted','Report Sent',
            'Cost Estimation Sent','Awaiting Funding Approval','Funding Approved',
            'Treatment Active','On Hold','Awaiting Further Funding','Discharged','Case Closed',
            'Not Proceeding'
        ) DEFAULT 'Enquiry Logged'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE patients MODIFY COLUMN status ENUM(
            'Enquiry Logged','Response Sent','Awaiting LOI','LOI Received',
            'Assessment Scheduled','Assessment Completed','Report Drafted','Report Sent',
            'Cost Estimation Sent','Awaiting Funding Approval','Funding Approved',
            'Treatment Active','Awaiting Further Funding','Discharged','Case Closed',
            'Not Proceeding'
        ) DEFAULT 'Enquiry Logged'");
    }
};
