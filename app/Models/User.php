<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    public const ROLE_DOKTER = 'dokter';
    public const ROLE_PERAWAT_UK = 'perawat_uk';
    public const ROLE_PERAWAT_BIASA = 'perawat_biasa';
    public const ROLE_ADMIN = 'admin';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function doctor(): HasOne
    {
        return $this->hasOne(Doctor::class);
    }

    public function nurse(): HasOne
    {
        return $this->hasOne(Nurse::class);
    }

    public function createdPatients(): HasMany
    {
        return $this->hasMany(Patient::class, 'created_by');
    }

    public function surgeryRequests(): HasMany
    {
        return $this->hasMany(SurgeryRequest::class, 'requested_by');
    }

    public function ukVerificationChecklists(): HasMany
    {
        return $this->hasMany(UkVerificationChecklist::class, 'verified_by');
    }

    public function surgerySchedulesApproved(): HasMany
    {
        return $this->hasMany(SurgerySchedule::class, 'approved_by');
    }

    public function surgeryHistories(): HasMany
    {
        return $this->hasMany(SurgeryHistory::class, 'changed_by');
    }

    public function guidelines(): HasMany
    {
        return $this->hasMany(Guideline::class, 'uploaded_by');
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(UserNotification::class, 'user_id');
    }

    public function isDoctor(): bool
    {
        return $this->role === self::ROLE_DOKTER;
    }

    public function isNurse(): bool
    {
        return in_array($this->role, [self::ROLE_PERAWAT_UK, self::ROLE_PERAWAT_BIASA], true);
    }

    public function isUkNurse(): bool
    {
        return $this->role === self::ROLE_PERAWAT_UK;
    }

    public function isRegularNurse(): bool
    {
        return $this->role === self::ROLE_PERAWAT_BIASA;
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }
}
