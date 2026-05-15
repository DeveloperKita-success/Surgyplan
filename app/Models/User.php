<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable;
    use HasRoles;

    public const ROLE_DOKTER = 'dokter';
    public const ROLE_PERAWAT = 'perawat';

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

    public function isDoctor(): bool
    {
        return $this->role === self::ROLE_DOKTER;
    }

    public function isNurse(): bool
    {
        return $this->role === self::ROLE_PERAWAT;
    }

    public function isOkNurse(): bool
    {
        return $this->isNurse() && optional($this->nurse)->type === 'ok';
    }

    public function isRegularNurse(): bool
    {
        return $this->isNurse() && optional($this->nurse)->type === 'biasa';
    }
}
