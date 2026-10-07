<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Services\InventoryService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('notifications:check-low-stock')]
#[Description('Send a staff alert when a product newly reaches its low-stock threshold')]
class CheckLowStock extends Command
{
    public function handle(InventoryService $inventory): int
    {
        Product::query()
            ->where('low_stock_alerted_quantity', '!=', null)
            ->whereColumn('stock_quantity', '>', 'low_stock_threshold')
            ->update(['low_stock_alerted_quantity' => null]);

        $checked = 0;

        Product::query()
            ->whereColumn('stock_quantity', '<=', 'low_stock_threshold')
            ->orderBy('id')
            ->chunkById(100, function ($products) use ($inventory, &$checked): void {
                foreach ($products as $product) {
                    $inventory->alertIfLowStock($product);
                    $checked++;
                }
            });

        $this->info("Checked {$checked} low-stock products.");

        return self::SUCCESS;
    }
}
