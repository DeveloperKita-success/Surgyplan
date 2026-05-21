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
        'diagnosis_text',
        'procedure_text',
        'requested_by',
        'requested_doctor_id',
        'requested_date',
        'requested_start_time',
        'requested_end_time',
        'risk_estimation',
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

    public function okVerificationChecklist(): HasOne
    {
        return $this->hasOne(OkVerificationChecklist::class);
    }

    public function okVerificationChecklistItems(): HasMany
    {
        return $this->hasMany(OkVerificationChecklistItem::class);
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
