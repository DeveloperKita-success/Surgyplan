<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OkVerificationChecklist extends Model
{
    use HasFactory;

    protected $fillable = [
        'surgery_request_id',
        'verified_by',
        'patient_wristband_installed',
        'doctor_present',
        'oxygen_saturation',
        'operating_room_ready',
        'anesthesiologist_name',
        'anesthesia_type',
        'asa_status',
        'anesthesia_approved',
        'doctor_anesthesia_approved',
        'anesthesia_note',
        'verification_note',
    ];

    protected $casts = [
        'patient_wristband_installed' => 'bool',
        'doctor_present' => 'bool',
        'operating_room_ready' => 'bool',
        'anesthesia_approved' => 'bool',
        'doctor_anesthesia_approved' => 'bool',
    ];

    public function surgeryRequest(): BelongsTo
    {
        return $this->belongsTo(SurgeryRequest::class);
    }

    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}
