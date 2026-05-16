<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OperationRoom extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'location',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'bool',
    ];

    public function operationBookings(): HasMany
    {
        return $this->hasMany(OperationBooking::class);
    }
}
