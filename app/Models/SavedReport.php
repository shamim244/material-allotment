<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SavedReport extends Model
{
    protected $fillable = ['user_id', 'name', 'meter_numbers'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
