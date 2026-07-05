<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReferralBill extends Model
{
    protected $fillable = [
        'referral_id', 'bill_date', 'amount', 'status', 'document_path', 'notes', 'created_by',
    ];

    protected $casts = ['bill_date' => 'date', 'amount' => 'decimal:2'];

    public function referral() { return $this->belongsTo(Referral::class); }
    public function createdBy() { return $this->belongsTo(User::class, 'created_by'); }
}
