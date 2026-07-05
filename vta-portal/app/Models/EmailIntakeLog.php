<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailIntakeLog extends Model
{
    protected $casts = [
        'received_at' => 'datetime',
        'processed_at' => 'datetime',
        'has_attachments' => 'boolean',
        'processed' => 'boolean',
    ];

    protected $fillable = [
        'from_email', 'from_name', 'subject', 'body', 'received_at',
        'has_attachments', 'attachment_paths', 'processed', 'linked_patient_id',
        'linked_case_manager_id', 'enquiry_id', 'vta_invoice_id', 'funding_cycle_id',
        'action_taken', 'processed_by', 'processed_at', 'notes'
    ];

    public function linkedPatient()
    {
        return $this->belongsTo(Patient::class, 'linked_patient_id');
    }

    public function linkedCaseManager()
    {
        return $this->belongsTo(CaseManager::class, 'linked_case_manager_id');
    }

    public function enquiry()
    {
        return $this->belongsTo(Enquiry::class);
    }

    public function vtaInvoice()
    {
        return $this->belongsTo(VtaInvoice::class, 'vta_invoice_id');
    }

    public function fundingCycle()
    {
        return $this->belongsTo(FundingCycle::class, 'funding_cycle_id');
    }

    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }
}
