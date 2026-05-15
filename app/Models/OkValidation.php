<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OkValidation extends Model
{
    use HasFactory;

    protected $fillable = [
        'operation_booking_id',
        'validated_by',
        'status',
        'validated_at',
        'notes',
    ];

    protected $casts = [
        'validated_at' => 'datetime',
    ];

    public function operationBooking(): BelongsTo
    {
        return $this->belongsTo(OperationBooking::class);
    }

    public function validatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'validated_by');
    }
}
