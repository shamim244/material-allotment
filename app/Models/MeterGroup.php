<?php

namespace App\Models;

use App\Models\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MeterGroup extends Model
{
    use BelongsToCompany, HasFactory;

    protected $fillable = [
        'company_id',
        'name',
        'type', // box, normal, large
        'lot_id',
    ];

    public function lot()
    {
        return $this->belongsTo(Lot::class);
    }

    public function meters()
    {
        return $this->hasMany(Meter::class, 'group_id');
    }
}
