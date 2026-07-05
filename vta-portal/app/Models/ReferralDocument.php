<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReferralDocument extends Model
{
    protected $fillable = [
        'referral_id', 'title', 'file_path', 'visible_to_associate', 'uploaded_by',
        'revision_requested', 'revision_notes',
    ];

    protected $casts = [
        'visible_to_associate' => 'boolean',
        'revision_requested'   => 'boolean',
    ];

    public function referral() { return $this->belongsTo(Referral::class); }
    public function uploadedBy() { return $this->belongsTo(User::class, 'uploaded_by'); }
}
