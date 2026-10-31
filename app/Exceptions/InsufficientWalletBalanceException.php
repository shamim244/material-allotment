<?php

namespace App\Exceptions;

use Exception;

class InsufficientWalletBalanceException extends Exception
{
    protected float $currentBalance;

    protected float $requestedAmount;

    public function __construct(float $currentBalance, float $requestedAmount, string $message = '')
    {
        $this->currentBalance = $currentBalance;
        $this->requestedAmount = $requestedAmount;

        if (empty($message)) {
            $message = sprintf(
                'Insufficient wallet balance. Available: ₹%.2f, Required: ₹%.2f',
                $currentBalance,
                $requestedAmount
            );
        }

        parent::__construct($message);
    }

    public function getCurrentBalance(): float
    {
        return $this->currentBalance;
    }

    public function getRequestedAmount(): float
    {
        return $this->requestedAmount;
    }
}
