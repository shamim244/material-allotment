<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'badge',
        'max_meters',
        'max_workers',
        'price_1m',
        'price_3m',
        'price_6m',
        'price_1y',
        'features',
        'status',
        'is_featured',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'max_meters' => 'integer',
            'max_workers' => 'integer',
            'price_1m' => 'float',
            'price_3m' => 'float',
            'price_6m' => 'float',
            'price_1y' => 'float',
            'features' => 'array',
            'is_featured' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopePublic($query)
    {
        return $query->whereIn('status', ['active', 'promo']);
    }

    /**
     * Get price for given cycle
     */
    public function priceForCycle(string $cycle): float
    {
        return match ($cycle) {
            '1m' => (float) $this->price_1m,
            '3m' => (float) $this->price_3m,
            '6m' => (float) $this->price_6m,
            '1y' => (float) $this->price_1y,
            default => (float) $this->price_1m,
        };
    }

    /**
     * Get duration in days for cycle
     */
    public static function daysForCycle(string $cycle): int
    {
        return match ($cycle) {
            '1m' => 30,
            '3m' => 90,
            '6m' => 180,
            '1y' => 365,
            default => 30,
        };
    }

    /**
     * Cycle label
     */
    public static function cycleLabel(string $cycle): string
    {
        return match ($cycle) {
            '1m' => '1 Month (30 Days)',
            '3m' => '3 Months (90 Days)',
            '6m' => '6 Months (180 Days)',
            '1y' => '1 Year (365 Days)',
            'custom' => 'Custom Deal',
            default => ucfirst($cycle),
        };
    }
}
