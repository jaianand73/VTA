<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Assessment extends Model
{
    protected $fillable = [
        'patient_id', 'fee_agreed_amount', 'fee_agreed_document_path',
        'date_client_contacted', 'assessor', 'venue', 'assessment_date',
        'assessment_cost', 'assessment_cost_document_path',
        'report_sent', 'report_document_path', 'special_instructions', 'notes', 'created_by'
    ];

    protected $casts = [
        'assessment_date' => 'date',
        'date_client_contacted' => 'date',
        'report_sent' => 'boolean',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function vtaInvoice(): HasOne
    {
        return $this->hasOne(VtaInvoice::class);
    }
}
