<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OperatingRoom extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_code',
        'room_name',
        'capacity',
        'description',
        'status',
    ];

    public function surgerySchedules(): HasMany
    {
        return $this->hasMany(SurgerySchedule::class);
    }

    public function facilities(): HasMany
    {
        return $this->hasMany(RoomFacility::class);
    }
}
