<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    protected $casts = [
        'date_of_birth' => 'date',
        'referral_date' => 'date',
        'first_contact_date' => 'date',
        'discharge_date' => 'date',
        'needs_review' => 'boolean',
    ];

    protected $fillable = [
        'case_manager_id', 'first_name', 'last_name', 'date_of_birth', 'location',
        'condition', 'status', 'referral_date', 'first_contact_date', 'discharge_date',
        'invoice_recipient_type', 'invoice_recipient_name', 'invoice_recipient_email',
        'invoice_recipient_address', 'assigned_staff_id', 'needs_review', 'folder_path',
        'notes', 'created_by'
    ];

    public function caseManager()
    {
        return $this->belongsTo(CaseManager::class);
    }

    public function assignedStaff()
    {
        return $this->belongsTo(User::class, 'assigned_staff_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function patientAssociates()
    {
        return $this->hasMany(PatientAssociate::class);
    }

    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    public function communications()
    {
        return $this->hasMany(Communication::class);
    }

    public function caseNotes()
    {
        return $this->hasMany(CaseNote::class);
    }

    public function costEstimations()
    {
        return $this->hasMany(CostEstimation::class);
    }

    public function fundingCycles()
    {
        return $this->hasMany(FundingCycle::class);
    }

    public function vtaInvoices()
    {
        return $this->hasMany(VtaInvoice::class);
    }

    public function associateInvoices()
    {
        return $this->hasMany(AssociateInvoice::class);
    }
}
