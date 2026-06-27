<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VtaInvoice extends Model
{
    protected $casts = [
        'invoice_date' => 'date',
        'due_date' => 'date',
        'payment_date' => 'date',
    ];

    protected $fillable = [
        'patient_id', 'funding_cycle_id', 'invoice_number', 'invoice_date',
        'due_date', 'recipient_type', 'recipient_name', 'recipient_email',
        'recipient_address', 'sessions_invoiced', 'session_amount',
        'additional_charges', 'total_amount', 'status', 'payment_date',
        'document_path', 'notes', 'created_by'
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function fundingCycle()
    {
        return $this->belongsTo(FundingCycle::class, 'funding_cycle_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
