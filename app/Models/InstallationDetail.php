<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InstallationDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'meter_id',
        'worker_id',
        'install_type',
        'request_number',
        'ca_number',
        'old_meter_number',
        'mobile_number',
        'latitude',
        'longitude',
        'remark',
    ];

    public function meter()
    {
        return $this->belongsTo(Meter::class);
    }

    public function worker()
    {
        return $this->belongsTo(User::class, 'worker_id');
    }

    public function images()
    {
        return $this->hasMany(InstallationImage::class);
    }
}
