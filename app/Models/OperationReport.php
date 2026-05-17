<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OperationReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'surgery_schedule_id',
        'doctor_id',
        'operation_result',
        'complication',
        'post_operation_note',
        'status',
    ];

    public function surgerySchedule(): BelongsTo
    {
        return $this->belongsTo(SurgerySchedule::class, 'surgery_schedule_id');
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }
}
