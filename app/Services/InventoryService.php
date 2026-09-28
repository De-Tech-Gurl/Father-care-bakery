<?php

namespace App\Services;

use App\Enums\InventoryReason;
use App\Models\InventoryMovement;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class InventoryService
{
    public function record(
        Product $product,
        int $quantityChange,
        InventoryReason $reason,
        ?string $reference = null,
        ?int $userId = null,
    ): InventoryMovement {
        $newStock = $product->stock_quantity + $quantityChange;

        if ($newStock < 0) {
            throw ValidationException::withMessages([
                'quantity' => "Not enough stock available for {$product->name}.",
            ]);
        }

        $product->update(['stock_quantity' => $newStock]);

        return InventoryMovement::query()->create([
            'product_id' => $product->id,
            'quantity_change' => $quantityChange,
            'reason' => $reason,
            'reference' => $reference,
            'created_by' => $userId ?? Auth::id(),
        ]);
    }
}
