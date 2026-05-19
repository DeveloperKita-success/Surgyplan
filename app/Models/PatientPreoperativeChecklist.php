<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PatientPreoperativeChecklist extends Model
{
    use HasFactory;

    protected $fillable = [
        'surgery_request_id',
        'surgical_consent',
        'surgical_consent_file',
        'anesthesia_consent',
        'anesthesia_consent_file',
        'lab_result_complete',
        'lab_result_file',
        'radiology_available',
        'radiology_file',
        'anesthesia_consultation_done',
        'anesthesia_risk_estimation',
        'vital_sign_stable',
        'vital_sign_note',
        'blood_pressure',
        'allergy',
        'fasting_more_than_6_hours',
        'blood_type',
        'blood_available',
        'infusion_installed',
        'catheter_installed',
        'surgical_area_shaved',
        'jewelry_removed',
        'disease_history',
        'current_medications',
        'has_previous_surgery',
        'previous_surgery_note',
        'previous_surgery_date',
        'final_note',
    ];

    protected $casts = [
        'surgical_consent' => 'bool',
        'anesthesia_consent' => 'bool',
        'lab_result_complete' => 'bool',
        'radiology_available' => 'bool',
        'previous_surgery_date' => 'date',
    ];

    public function surgeryRequest(): BelongsTo
    {
        return $this->belongsTo(SurgeryRequest::class);
    }
}
