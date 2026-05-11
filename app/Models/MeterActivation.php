<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MeterActivation extends Model
{
    use HasFactory;

    protected $fillable = [
        'meter_id',
        'worker_id',
        'mi_status',
        'issue_type_id',
        'issue_detail',
        'remark',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'completed_at' => 'datetime',
        ];
    }

    public function meter()
    {
        return $this->belongsTo(Meter::class);
    }

    public function worker()
    {
        return $this->belongsTo(User::class, 'worker_id');
    }

    public function issueType()
    {
        return $this->belongsTo(MiIssueType::class, 'issue_type_id');
    }

    public function images()
    {
        return $this->hasMany(MeterActivationImage::class);
    }
}
