<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PatientPreparation extends Model
{
    use HasFactory;

    protected $fillable = [
        'operation_booking_id',
        'prepared_by',
        'fasting_confirmed',
        'consent_signed',
        'labs_complete',
        'prepared_at',
        'notes',
    ];

    protected $casts = [
        'fasting_confirmed' => 'bool',
        'consent_signed' => 'bool',
        'labs_complete' => 'bool',
        'prepared_at' => 'datetime',
    ];

    public function operationBooking(): BelongsTo
    {
        return $this->belongsTo(OperationBooking::class);
    }

    public function preparedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'prepared_by');
    }
}
