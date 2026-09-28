<?php

namespace App\Enums;

enum PaymentStatus: string
{
    case PENDING = 'pending';
    case AWAITING_PAYMENT = 'awaiting_payment';
    case PAID = 'paid';
    case FAILED = 'failed';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Pending',
            self::AWAITING_PAYMENT => 'Awaiting payment',
            self::PAID => 'Paid',
            self::FAILED => 'Payment failed',
        };
    }

    public function isPaid(): bool
    {
        return $this === self::PAID;
    }
}
