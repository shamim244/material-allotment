<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MeterActivationImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'meter_activation_id',
        'image_path',
        'uploaded_by',
    ];

    public function activation()
    {
        return $this->belongsTo(MeterActivation::class, 'meter_activation_id');
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
