<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Patient extends Model
{
    protected $casts = [
        'date_of_birth' => 'date',
        'referral_date' => 'date',
        'first_contact_date' => 'date',
        'discharge_date' => 'date',
        'needs_review' => 'boolean',
        'assessment_report_sent' => 'boolean',
        'fee_agreed_amount' => 'decimal:2',
    ];

    protected $fillable = [
        'patient_ref', 'case_manager_id', 'first_name', 'last_name', 'email', 'phone', 'address',
        'address_line_1', 'address_line_2', 'city', 'postcode',
        'date_of_birth', 'location', 'condition', 'status', 'referral_date',
        'first_contact_date', 'discharge_date',
        'fee_agreed_amount', 'fee_agreed_document', 'assessment_report_sent', 'assessment_report_document',
        'invoice_recipient_type', 'invoice_recipient_name', 'invoice_recipient_email',
        'invoice_recipient_address', 'assigned_staff_id', 'needs_review', 'folder_path',
        'notes', 'clinical_alert', 'created_by', 'enquiry_id', 'referral_id',
    ];

    public function enquiry()
    {
        return $this->belongsTo(Enquiry::class);
    }

    public function referral()
    {
        return $this->belongsTo(\App\Models\Referral::class);
    }

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

    public function appointments()
    {
        return $this->hasMany(\App\Models\Appointment::class);
    }

    public function mdtMeetings()
    {
        return $this->hasMany(PatientMdtMeeting::class)->orderBy('meeting_date', 'desc');
    }

    public function assessment(): HasOne
    {
        return $this->hasOne(Assessment::class);
    }

    public function referrers(): HasMany
    {
        return $this->hasMany(PatientReferrer::class);
    }

    public function nextOfKin(): HasMany
    {
        return $this->hasMany(PatientNextOfKin::class, 'patient_id');
    }

    public function canTransitionTo(string $newStatus): bool
    {
        $order = [
            'Enquiry Logged', 'Response Sent', 'Awaiting LOI', 'LOI Received',
            'Assessment Scheduled', 'Assessment Completed', 'Report Drafted', 'Report Sent',
            'Cost Estimation Sent', 'Awaiting Funding Approval', 'Funding Approved',
            'Treatment Active', 'Awaiting Further Funding', 'Discharged', 'Case Closed'
        ];
        $currentIndex = array_search($this->status, $order);
        $newIndex = array_search($newStatus, $order);
        return $currentIndex !== false && $newIndex !== false && $newIndex > $currentIndex;
    }
}
