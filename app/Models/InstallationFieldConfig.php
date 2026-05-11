<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InstallationFieldConfig extends Model
{
    use HasFactory;

    protected $fillable = [
        'field_name',
        'label',
        'install_type',
        'is_required',
        'is_enabled',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_required' => 'boolean',
            'is_enabled' => 'boolean',
        ];
    }

    /**
     * Get all field configs keyed by field_name.
     */
    public static function getConfig()
    {
        return static::orderBy('sort_order')->get()->keyBy('field_name');
    }
}
