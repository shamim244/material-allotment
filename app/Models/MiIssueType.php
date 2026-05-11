<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MiIssueType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function activations()
    {
        return $this->hasMany(MeterActivation::class, 'issue_type_id');
    }

    /**
     * Get active issue types ordered by sort_order.
     */
    public static function active()
    {
        return static::where('is_active', true)->orderBy('sort_order')->get();
    }
}
