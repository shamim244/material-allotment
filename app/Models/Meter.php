<?php

namespace App\Models;

use App\Models\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Meter extends Model
{
    use BelongsToCompany, HasFactory, SoftDeletes;

    protected $fillable = [
        'company_id',
        'meter_number',
        'group_id',
        'lot_id',
        'status', // stock, assigned, installed, issue, returned
        'current_worker_id',
    ];

    public function lot()
    {
        return $this->belongsTo(Lot::class);
    }

    public function group()
    {
        return $this->belongsTo(MeterGroup::class);
    }

    public function currentWorker()
    {
        return $this->belongsTo(User::class, 'current_worker_id');
    }

    public function assignments()
    {
        return $this->hasMany(Assignment::class);
    }

    public function logs()
    {
        return $this->hasMany(MeterLog::class);
    }

    public function installationDetail()
    {
        return $this->hasOne(InstallationDetail::class);
    }

    public function activation()
    {
        return $this->hasOne(MeterActivation::class);
    }

    public function returnRequests()
    {
        return $this->hasMany(ReturnRequest::class);
    }
}
