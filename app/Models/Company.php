<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'contact_person',
        'email',
        'phone',
        'address',
        'status',
        'max_workers',
        'max_meters',
    ];

    protected function casts(): array
    {
        return [
            'max_workers' => 'integer',
            'max_meters' => 'integer',
        ];
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function contractors()
    {
        return $this->hasMany(User::class)->where('role', 'contractor');
    }

    public function workers()
    {
        return $this->hasMany(User::class)->where('role', 'worker');
    }

    public function meters()
    {
        return $this->hasMany(Meter::class);
    }

    public function lots()
    {
        return $this->hasMany(Lot::class);
    }

    public function meterGroups()
    {
        return $this->hasMany(MeterGroup::class);
    }

    public function assignments()
    {
        return $this->hasMany(Assignment::class);
    }

    public function meterActivations()
    {
        return $this->hasMany(MeterActivation::class);
    }

    public function returnRequests()
    {
        return $this->hasMany(ReturnRequest::class);
    }

    public function accountingLedger()
    {
        return $this->hasMany(AccountingLedger::class);
    }

    public function savedReports()
    {
        return $this->hasMany(SavedReport::class);
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isSuspended(): bool
    {
        return $this->status === 'suspended';
    }
}
