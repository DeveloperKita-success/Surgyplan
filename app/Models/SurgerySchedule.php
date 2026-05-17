<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SurgerySchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'surgery_request_id',
        'patient_id',
        'doctor_id',
        'operating_room_id',
        'approved_by',
        'surgery_date',
        'start_time',
        'end_time',
        'schedule_status',
        'rejection_reason',
        'delay_reason',
        'completed_at',
    ];

    protected $casts = [
        'surgery_date' => 'date',
        'completed_at' => 'datetime',
    ];

    public function surgeryRequest(): BelongsTo
    {
        return $this->belongsTo(SurgeryRequest::class);
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    public function operatingRoom(): BelongsTo
    {
        return $this->belongsTo(OperatingRoom::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function operationReports(): HasMany
    {
        return $this->hasMany(OperationReport::class, 'surgery_schedule_id');
    }
}
