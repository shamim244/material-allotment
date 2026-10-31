<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'plan_id',
        'plan_name',
        'billing_cycle',
        'duration_days',
        'starts_at',
        'ends_at',
        'price',
        'wallet_discount',
        'amount_paid',
        'payment_method',
        'status',
        'max_meters',
        'max_workers',
        'is_custom_deal',
        'custom_deal_notes',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'duration_days' => 'integer',
            'price' => 'float',
            'wallet_discount' => 'float',
            'amount_paid' => 'float',
            'max_meters' => 'integer',
            'max_workers' => 'integer',
            'is_custom_deal' => 'boolean',
        ];
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Check if this subscription is currently active
     */
    public function isActive(): bool
    {
        return $this->status === 'active' && $this->ends_at->isFuture();
    }

    /**
     * Check if expired
     */
    public function isExpired(): bool
    {
        return $this->status === 'expired' || $this->ends_at->isPast();
    }

    /**
     * Days remaining until expiration
     */
    public function daysRemaining(): int
    {
        if ($this->ends_at->isPast()) {
            return 0;
        }

        return (int) ceil(now()->diffInDays($this->ends_at, false));
    }

    /**
     * Is subscription expiring within X days
     */
    public function isExpiringSoon(int $days = 7): bool
    {
        if (! $this->isActive()) {
            return false;
        }

        $remaining = $this->daysRemaining();

        return $remaining >= 0 && $remaining <= $days;
    }
}
