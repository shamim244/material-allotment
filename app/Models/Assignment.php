<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToCompany;

class Assignment extends Model
{
    use HasFactory, BelongsToCompany;

    protected $fillable = [
        'company_id',
        'meter_id',
        'worker_id',
        'assigned_by',
        'assigned_at',
        'batch_id',
    ];

    public function meter()
    {
        return $this->belongsTo(Meter::class);
    }

    public function worker()
    {
        return $this->belongsTo(User::class, 'worker_id');
    }

    public function assigner()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }
}
