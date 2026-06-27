<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssociateInvoice extends Model
{
    protected $casts = [
        'invoice_date' => 'date',
        'payment_date' => 'date',
        'due_date' => 'date',
    ];

    protected $fillable = [
        'associate_id', 'patient_id', 'funding_cycle_id', 'invoice_reference',
        'invoice_date', 'sessions_completed', 'travel_miles', 'session_amount',
        'travel_amount', 'total_amount', 'status', 'payment_date', 'due_date',
        'document_path', 'notes', 'logged_by'
    ];

    public function associate()
    {
        return $this->belongsTo(Associate::class);
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function fundingCycle()
    {
        return $this->belongsTo(FundingCycle::class, 'funding_cycle_id');
    }

    public function loggedBy()
    {
        return $this->belongsTo(User::class, 'logged_by');
    }
}
