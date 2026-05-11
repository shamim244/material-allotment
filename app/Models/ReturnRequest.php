<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReturnRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'meter_id',
        'worker_id',
        'reason',
        'status',
        'admin_remark',
    ];

    public function meter()
    {
        return $this->belongsTo(Meter::class);
    }

    public function worker()
    {
        return $this->belongsTo(User::class, 'worker_id');
    }
}
