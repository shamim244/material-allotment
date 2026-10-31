<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Company extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'referral_code',
        'referred_by_id',
        'contact_person',
        'email',
        'phone',
        'address',
        'status',
        'wallet_balance',
        'max_workers',
        'max_meters',
    ];

    protected function casts(): array
    {
        return [
            'wallet_balance' => 'float',
            'max_workers' => 'integer',
            'max_meters' => 'integer',
        ];
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function contractors()
    {
        return $this->hasMany(User::class)->where('role', 'contractor');
    }

    public function workers()
    {
        return $this->hasMany(User::class)->where('role', 'worker');
    }

    public function meters()
    {
        return $this->hasMany(Meter::class);
    }

    public function lots()
    {
        return $this->hasMany(Lot::class);
    }

    public function meterGroups()
    {
        return $this->hasMany(MeterGroup::class);
    }

    public function assignments()
    {
        return $this->hasMany(Assignment::class);
    }

    public function meterActivations()
    {
        return $this->hasMany(MeterActivation::class);
    }

    public function returnRequests()
    {
        return $this->hasMany(ReturnRequest::class);
    }

    public function accountingLedger()
    {
        return $this->hasMany(AccountingLedger::class);
    }

    public function savedReports()
    {
        return $this->hasMany(SavedReport::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class)->orderBy('ends_at', 'desc');
    }

    public function activeSubscription()
    {
        return $this->hasOne(Subscription::class)
            ->where('status', 'active')
            ->where('ends_at', '>', now())
            ->latestOfMany('ends_at');
    }

    public function currentSubscription(): ?Subscription
    {
        return $this->subscriptions()
            ->where('status', 'active')
            ->where('ends_at', '>', now())
            ->orderBy('ends_at', 'desc')
            ->first();
    }

    public function latestSubscription(): ?Subscription
    {
        return $this->subscriptions()->first();
    }

    public function walletTransactions()
    {
        return $this->hasMany(WalletTransaction::class)->orderBy('created_at', 'desc');
    }

    public function referralsMade()
    {
        return $this->hasMany(Referral::class, 'referrer_company_id');
    }

    public function referredBy()
    {
        return $this->belongsTo(Company::class, 'referred_by_id');
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isSuspended(): bool
    {
        return $this->status === 'suspended';
    }

    public function hasActiveSubscription(): bool
    {
        return $this->currentSubscription() !== null;
    }

    public function daysRemainingOnSubscription(): int
    {
        $sub = $this->currentSubscription();

        return $sub ? $sub->daysRemaining() : 0;
    }

    public function isSubscriptionExpiringSoon(int $days = 7): bool
    {
        $sub = $this->currentSubscription();

        return $sub ? $sub->isExpiringSoon($days) : false;
    }

    public function isSubscriptionExpired(): bool
    {
        $current = $this->currentSubscription();
        if ($current) {
            return false;
        }

        $latest = $this->latestSubscription();

        return $latest !== null && $latest->isExpired();
    }

    public function hasNeverSubscribed(): bool
    {
        return $this->latestSubscription() === null;
    }

    /**
     * Generate unique referral code for company
     */
    public static function generateUniqueReferralCode(string $prefix = 'NCC-REF'): string
    {
        do {
            $code = $prefix.'-'.strtoupper(Str::random(6));
        } while (static::where('referral_code', $code)->exists());

        return $code;
    }
}
