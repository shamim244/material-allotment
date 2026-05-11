<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MeterLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'meter_id',
        'action',
        'user_id',
        'remark',
        'timestamp',
    ];

    public function meter()
    {
        return $this->belongsTo(Meter::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
