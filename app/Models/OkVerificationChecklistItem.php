<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OkVerificationChecklistItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'surgery_request_id',
        'name',
        'value',
        'note',
        'file',
    ];

    protected $casts = [
        'value' => 'bool',
    ];

    public function surgeryRequest(): BelongsTo
    {
        return $this->belongsTo(SurgeryRequest::class);
    }
}
