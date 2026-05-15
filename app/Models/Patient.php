<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Patient extends Model
{
    use HasFactory;

    protected $fillable = [
        'medical_record_number',
        'name',
        'gender',
        'birth_date',
        'phone',
        'address',
        'source_unit',
    ];

    protected $casts = [
        'birth_date' => 'date',
    ];

    public function operationBookings(): HasMany
    {
        return $this->hasMany(OperationBooking::class);
    }
}
