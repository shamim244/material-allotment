<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Traits\BelongsToCompany;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use BelongsToCompany, HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'company_id',
        'name',
        'email',
        'mobile',
        'password',
        'plain_password',
        'role',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
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
            'is_active' => 'boolean',
        ];
    }

    /**
     * Two-way encryption for plain_password so raw DB storage is encrypted
     * while admin reveals and controller access remain transparent.
     */
    protected function plainPassword(): Attribute
    {
        return Attribute::make(
            get: function (?string $value) {
                if (empty($value)) {
                    return $value;
                }
                try {
                    return Crypt::decryptString($value);
                } catch (DecryptException $e) {
                    return $value;
                }
            },
            set: function (?string $value) {
                if (empty($value)) {
                    return $value;
                }
                try {
                    Crypt::decryptString($value);

                    return $value;
                } catch (DecryptException $e) {
                    return Crypt::encryptString($value);
                }
            }
        );
    }

    public function meters()
    {
        return $this->hasMany(Meter::class, 'current_worker_id');
    }

    public function meterActivations()
    {
        return $this->hasMany(MeterActivation::class, 'worker_id');
    }

    public function assignments()
    {
        return $this->hasMany(Assignment::class, 'worker_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    public function isContractor(): bool
    {
        return $this->role === 'contractor';
    }

    public function isWorker(): bool
    {
        return $this->role === 'worker';
    }

    protected static function booted(): void
    {
        static::creating(function ($user) {
            if (empty($user->company_id) && Auth::hasUser()) {
                $auth = Auth::user();
                if ($auth->role !== 'super_admin' && ! empty($auth->company_id)) {
                    $user->company_id = $auth->company_id;
                }
            }
        });
    }
}
