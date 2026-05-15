<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class OperationBooking extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'operation_room_id',
        'doctor_id',
        'created_by',
        'scheduled_at',
        'status',
        'is_emergency',
        'notes',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'is_emergency' => 'bool',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function operationRoom(): BelongsTo
    {
        return $this->belongsTo(OperationRoom::class);
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function patientPreparation(): HasOne
    {
        return $this->hasOne(PatientPreparation::class);
    }

    public function okValidation(): HasOne
    {
        return $this->hasOne(OkValidation::class);
    }
}
