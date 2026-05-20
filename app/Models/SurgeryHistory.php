<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SurgeryHistory extends Model
{
    use HasFactory;

    public $timestamps = false;

    public const CREATED_AT = 'created_at';

    public const UPDATED_AT = null;

    protected $fillable = [
        'surgery_request_id',
        'changed_by',
        'old_status',
        'new_status',
        'note',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function surgeryRequest(): BelongsTo
    {
        return $this->belongsTo(SurgeryRequest::class);
    }

    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
