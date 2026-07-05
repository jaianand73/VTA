<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Referral extends Model
{
    protected $fillable = [
        'referral_ref', 'enquiry_id',
        'patient_first_name', 'patient_last_name', 'patient_dob',
        'patient_address', 'patient_postcode', 'patient_phone', 'patient_email',
        'company_id', 'case_manager_id',
        'special_instructions',
        'visit_approved_date', 'visit_approved_document',
        'associate_id',
        'proposal_submitted_date', 'proposal_document', 'proposal_approved_date',
        'status', 'notes', 'created_by',
    ];

    protected $casts = [
        'patient_dob'             => 'date',
        'visit_approved_date'     => 'date',
        'proposal_submitted_date' => 'date',
        'proposal_approved_date'  => 'date',
    ];

    public function enquiry()
    {
        return $this->belongsTo(Enquiry::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function caseManager()
    {
        return $this->belongsTo(CaseManager::class);
    }

    public function associate()
    {
        return $this->belongsTo(Associate::class);
    }

    public function sessions()       { return $this->hasMany(ReferralSession::class)->latest('session_date'); }
    public function bills()          { return $this->hasMany(ReferralBill::class)->latest('bill_date'); }
    public function communications() { return $this->hasMany(ReferralCommunication::class)->latest('communication_date'); }
    public function documents()      { return $this->hasMany(ReferralDocument::class)->latest(); }

    public function patient()
    {
        return $this->hasOne(Patient::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getPatientFullNameAttribute(): string
    {
        return trim($this->patient_first_name . ' ' . $this->patient_last_name);
    }

    public function isApproved(): bool
    {
        return $this->status === 'Approved';
    }

    public function hasVisitApproval(): bool
    {
        return !is_null($this->visit_approved_date);
    }
}
