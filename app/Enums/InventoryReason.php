<?php

namespace App\Enums;

enum InventoryReason: string
{
    case SALE = 'sale';
    case RESTOCK = 'restock';
    case ADJUSTMENT = 'adjustment';
    case WASTE = 'waste';
    case ORDER_CANCELLED = 'order_cancelled';

    public function label(): string
    {
        return match ($this) {
            self::SALE => 'Sale',
            self::RESTOCK => 'Restock',
            self::ADJUSTMENT => 'Adjustment',
            self::WASTE => 'Waste',
            self::ORDER_CANCELLED => 'Order cancelled',
        };
    }
}
