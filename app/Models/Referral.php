<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Referral extends Model
{
    use HasFactory;

    protected $fillable = [
        'referrer_company_id',
        'referred_company_id',
        'reward_amount',
        'status',
        'rewarded_at',
        'wallet_transaction_id',
    ];

    protected function casts(): array
    {
        return [
            'reward_amount' => 'float',
            'rewarded_at' => 'datetime',
        ];
    }

    public function referrerCompany()
    {
        return $this->belongsTo(Company::class, 'referrer_company_id');
    }

    public function referredCompany()
    {
        return $this->belongsTo(Company::class, 'referred_company_id');
    }

    public function walletTransaction()
    {
        return $this->belongsTo(WalletTransaction::class);
    }
}
