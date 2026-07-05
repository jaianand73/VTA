<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UatTestResult extends Model
{
    protected $fillable = [
        'step_reference',
        'step_title',
        'result',
        'comment',
        'tested_by',
        'tested_at',
        'feedback_item_id',
    ];

    protected $casts = [
        'tested_at' => 'datetime',
    ];

    public function feedbackItem()
    {
        return $this->belongsTo(PortalFeedbackItem::class, 'feedback_item_id');
    }

    public function resultColour(): string
    {
        return match($this->result) {
            'pass'                  => 'green',
            'fail'                  => 'red',
            'pass_with_improvement' => 'amber',
            default                 => 'gray',
        };
    }

    public function resultLabel(): string
    {
        return match($this->result) {
            'pass'                  => 'Pass',
            'fail'                  => 'Fail',
            'pass_with_improvement' => 'Pass — Improvement noted',
            default                 => '—',
        };
    }
}
