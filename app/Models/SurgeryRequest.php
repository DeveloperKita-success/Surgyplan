<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class SurgeryRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'diagnosis_id',
        'procedure_id',
        'requested_by',
        'requested_doctor_id',
        'requested_date',
        'requested_start_time',
        'requested_end_time',
        'patient_priority',
        'request_status',
        'notes',
    ];

    protected $casts = [
        'requested_date' => 'date',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function diagnosis(): BelongsTo
    {
        return $this->belongsTo(Diagnosis::class);
    }

    public function procedure(): BelongsTo
    {
        return $this->belongsTo(Procedure::class);
    }

    public function requestedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function requestedDoctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class, 'requested_doctor_id');
    }

    public function preoperativeChecklist(): HasOne
    {
        return $this->hasOne(PatientPreoperativeChecklist::class);
    }

    public function preoperativeChecklistItems(): HasMany
    {
        return $this->hasMany(PatientPreoperativeChecklistItem::class);
    }

    public function ukVerificationChecklist(): HasOne
    {
        return $this->hasOne(UkVerificationChecklist::class);
    }

    public function ukVerificationChecklistItems(): HasMany
    {
        return $this->hasMany(UkVerificationChecklistItem::class);
    }

    public function surgerySchedules(): HasMany
    {
        return $this->hasMany(SurgerySchedule::class);
    }

    public function surgeryHistories(): HasMany
    {
        return $this->hasMany(SurgeryHistory::class);
    }
}
