<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CostEstimation extends Model
{
    protected $casts = [
        'sent_date' => 'date',
    ];

    protected $fillable = [
        'patient_id', 'version_number', 'title', 'estimated_amount',
        'estimated_sessions', 'estimated_duration', 'sent_date', 'sent_to',
        'notes', 'document_path', 'created_by'
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
