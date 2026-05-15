<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Nurse extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'unit_asal',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function preparations(): HasMany
    {
        return $this->hasMany(PatientPreparation::class, 'prepared_by', 'user_id');
    }

    public function okValidations(): HasMany
    {
        return $this->hasMany(OkValidation::class, 'validated_by', 'user_id');
    }
}
