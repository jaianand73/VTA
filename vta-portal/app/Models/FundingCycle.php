<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FundingCycle extends Model
{
    protected $casts = [
        'approval_date' => 'date',
        'is_active' => 'boolean',
    ];

    protected $fillable = [
        'patient_id', 'cost_estimation_id', 'cycle_number', 'approved_amount',
        'approved_sessions', 'approval_date', 'approval_document_path',
        'estimated_duration', 'funder_name', 'funder_reference', 'is_active',
        'notes', 'created_by'
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function costEstimation()
    {
        return $this->belongsTo(CostEstimation::class, 'cost_estimation_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function vtaInvoices()
    {
        return $this->hasMany(VtaInvoice::class, 'funding_cycle_id');
    }
}
