<?php

namespace App\Enums;

enum DeliveryType: string
{
    case PICKUP = 'pickup';
    case DELIVERY = 'delivery';

    public function label(): string
    {
        return match ($this) {
            self::PICKUP => 'Pickup at bakery',
            self::DELIVERY => 'Home delivery',
        };
    }
}
