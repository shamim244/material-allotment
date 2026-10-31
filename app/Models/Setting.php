<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToCompany;

class Setting extends Model
{
    use BelongsToCompany;

    protected $fillable = ['company_id', 'key', 'value', 'group', 'type'];
}
