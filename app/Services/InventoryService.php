<?php

namespace App\Services;

use App\Enums\InventoryReason;
use App\Events\NotificationRequested;
use App\Models\InventoryMovement;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
        $previousStock = $product->stock_quantity;
        $newStock = $product->stock_quantity + $quantityChange;

        if ($newStock < 0) {
            throw ValidationException::withMessages([
                'quantity' => "Not enough stock available for {$product->name}.",
            ]);
        }

        $product->update(['stock_quantity' => $newStock]);

        if ($newStock === 0 && $previousStock > 0) {
            event(new NotificationRequested(
                'inventory.out_of_stock',
                'Product is out of stock',
                $product->name.' has run out of stock.',
                'staff',
                url: route('admin.products.edit', $product),
                icon: 'ph-warning-circle',
                context: ['product_id' => $product->id, 'stock_quantity' => 0],
            ));
        }

        if ($quantityChange > 0) {
            event(new NotificationRequested(
                'inventory.restocked',
                'Product restocked',
                $product->name.' stock increased to '.$newStock.'.',
                'staff',
                url: route('admin.products.edit', $product),
                icon: 'ph-package',
                context: ['product_id' => $product->id, 'stock_quantity' => $newStock],
            ));
        }

        $this->alertIfLowStock($product);

        return InventoryMovement::query()->create([
            'product_id' => $product->id,
            'quantity_change' => $quantityChange,
            'reason' => $reason,
            'reference' => $reference,
            'created_by' => $userId ?? Auth::id(),
        ]);
    }

    public function alertIfLowStock(Product $product): bool
    {
        if (! $product->isLowStock()) {
            Product::query()
                ->whereKey($product->getKey())
                ->whereNotNull('low_stock_alerted_quantity')
                ->update(['low_stock_alerted_quantity' => null]);

            return false;
        }

        $claimed = Product::query()
            ->whereKey($product->getKey())
            ->whereColumn('stock_quantity', '<=', 'low_stock_threshold')
            ->where(function ($query): void {
                $query->whereNull('low_stock_alerted_quantity')
                    ->orWhereColumn('low_stock_alerted_quantity', '!=', 'stock_quantity');
            })
            ->update(['low_stock_alerted_quantity' => DB::raw('stock_quantity')]);

        if ($claimed === 0) {
            return false;
        }

        $product->refresh();

        event(new NotificationRequested(
            'inventory.low_stock',
            'Low stock attention',
            $product->name.' has '.$product->stock_quantity.' remaining (threshold: '.$product->low_stock_threshold.').',
            'staff',
            url: route('admin.products.edit', $product),
            icon: 'ph-warning',
            context: [
                'product_id' => $product->id,
                'stock_quantity' => $product->stock_quantity,
                'low_stock_threshold' => $product->low_stock_threshold,
            ],
        ));

        return true;
    }
}
