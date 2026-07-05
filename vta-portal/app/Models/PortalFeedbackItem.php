<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PortalFeedbackItem extends Model
{
    protected $fillable = [
        'type', 'section', 'reference', 'priority', 'title', 'description',
        'dev_context', 'samy_status', 'samy_response', 'samy_responded_at',
        'dev_status', 'dev_notes', 'client_notes', 'dev_follow_up', 'severity', 'raised_by', 'is_seeded', 'screenshots',
    ];

    protected $casts = [
        'samy_responded_at' => 'datetime',
        'is_seeded'         => 'boolean',
        'screenshots'       => 'array',
    ];

    public function scopeChanges($q) { return $q->where('type', 'change'); }
    public function scopeQuestions($q) { return $q->where('type', 'question'); }
    public function scopeImprovements($q) { return $q->where('type', 'improvement'); }
    public function scopeBugs($q) { return $q->where('type', 'bug'); }

    public function priorityColour(): string
    {
        return match($this->priority) {
            'critical' => 'red',
            'high'     => 'red',
            'medium'   => 'yellow',
            'low'      => 'green',
            'new'      => 'blue',
            default    => 'gray',
        };
    }

    public function samyStatusColour(): string
    {
        return match($this->samy_status) {
            'approved'  => 'green',
            'hold'      => 'yellow',
            'rejected'  => 'red',
            default     => 'gray',
        };
    }

    public function devStatusColour(): string
    {
        return match($this->dev_status) {
            'done'        => 'green',
            'in_progress' => 'blue',
            default       => 'gray',
        };
    }
}
