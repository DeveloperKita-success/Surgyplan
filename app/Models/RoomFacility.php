<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RoomFacility extends Model
{
    use HasFactory;

    protected $fillable = [
        'operating_room_id',
        'name',
        'description',
    ];

    public function operatingRoom(): BelongsTo
    {
        return $this->belongsTo(OperatingRoom::class);
    }
}
