<?php

namespace App\Models;

use App\Models\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class SavedReport extends Model
{
    use BelongsToCompany;

    protected $fillable = ['company_id', 'user_id', 'name', 'meter_numbers'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
