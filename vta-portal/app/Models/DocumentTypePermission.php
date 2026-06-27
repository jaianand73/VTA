<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentTypePermission extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'document_type_id', 'role', 'can_view', 'updated_by'
    ];

    public function documentType()
    {
        return $this->belongsTo(DocumentType::class);
    }
}
