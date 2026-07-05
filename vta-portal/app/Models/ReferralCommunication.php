<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReferralCommunication extends Model
{
    protected $fillable = [
        'referral_id', 'communication_date', 'type', 'direction', 'subject', 'notes', 'document_path', 'created_by',
    ];

    protected $casts = ['communication_date' => 'date'];

    public function referral() { return $this->belongsTo(Referral::class); }
    public function createdBy() { return $this->belongsTo(User::class, 'created_by'); }
}
