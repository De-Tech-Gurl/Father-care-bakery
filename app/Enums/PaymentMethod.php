<?php

namespace App\Enums;

enum PaymentMethod: string
{
    case BANK_TRANSFER = 'bank_transfer';
    case USSD = 'ussd';
    case CASH_ON_DELIVERY = 'cash_on_delivery';
    case CARD = 'card';

    public function label(): string
    {
        return match ($this) {
            self::BANK_TRANSFER => 'Bank transfer',
            self::USSD => 'USSD',
            self::CASH_ON_DELIVERY => 'Cash on delivery',
            self::CARD => 'Card payment',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::BANK_TRANSFER => 'Transfer to our bakery account, then upload your payment receipt to place the order.',
            self::USSD => 'Pay with your bank USSD code via Paystack.',
            self::CASH_ON_DELIVERY => 'Pay cash when your order is delivered.',
            self::CARD => 'Pay securely with your debit or credit card via Paystack.',
        };
    }

    public function usesPaystack(): bool
    {
        return in_array($this, [self::CARD, self::USSD], true);
    }

    /**
     * @return list<string>|null
     */
    public function paystackChannels(): ?array
    {
        return match ($this) {
            self::CARD => ['card'],
            self::USSD => ['ussd'],
            default => null,
        };
    }
}
