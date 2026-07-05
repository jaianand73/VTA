<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    public $timestamps = false;

    protected $casts = [
        'password_shared_date' => 'date',
        'is_password_protected' => 'boolean',
        'approved_at' => 'datetime',
    ];

    protected $fillable = [
        'document_type_id', 'patient_id', 'case_manager_id', 'appointment_id', 'enquiry_id',
        'file_name', 'stored_file_name', 'file_path', 'file_size', 'mime_type',
        'is_password_protected', 'report_password', 'password_shared_date',
        'password_shared_via', 'uploaded_by',
        'approval_status', 'approval_remarks', 'approved_by', 'approved_at',
    ];

    public function documentType()
    {
        return $this->belongsTo(DocumentType::class);
    }

    public function enquiry()
    {
        return $this->belongsTo(Enquiry::class);
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function caseManager()
    {
        return $this->belongsTo(CaseManager::class, 'case_manager_id');
    }

    public function appointment()
    {
        return $this->belongsTo(Appointment::class, 'appointment_id');
    }

    public function uploadedBy()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function isPending(): bool   { return $this->approval_status === 'pending'; }
    public function isApproved(): bool  { return $this->approval_status === 'approved'; }
    public function isRejected(): bool  { return $this->approval_status === 'rejected'; }
}
