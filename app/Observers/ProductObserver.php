<?php

namespace App\Observers;

use App\Events\NotificationRequested;
use App\Models\Product;
use App\Services\InventoryService;

class ProductObserver
{
    /**
     * Handle the Product "created" event.
     */
    public function created(Product $product): void
    {
        event(new NotificationRequested(
            'product.created',
            'New product added',
            $product->name.' was added to the catalog.',
            'staff',
            url: route('admin.products.edit', $product),
            icon: 'ph-cookie',
            context: ['product_id' => $product->id],
        ));

        app(InventoryService::class)->alertIfLowStock($product);
    }

    /**
     * Handle the Product "updated" event.
     */
    public function updated(Product $product): void
    {
        if ($product->wasChanged('price')) {
            event(new NotificationRequested(
                'product.price_changed',
                'Product price changed',
                sprintf('%s price changed from %s to %s.', $product->name, $product->getOriginal('price'), $product->price),
                'staff',
                url: route('admin.products.edit', $product),
                icon: 'ph-tag',
                context: ['product_id' => $product->id],
            ));
        }

        if ($product->wasChanged('is_active') && ! $product->is_active) {
            event(new NotificationRequested(
                'product.deactivated',
                'Product deactivated',
                $product->name.' is no longer available in the catalog.',
                'staff',
                url: route('admin.products.edit', $product),
                icon: 'ph-eye-slash',
                context: ['product_id' => $product->id],
            ));
        }
    }

    /**
     * Handle the Product "deleted" event.
     */
    public function deleted(Product $product): void
    {
        //
    }

    /**
     * Handle the Product "restored" event.
     */
    public function restored(Product $product): void
    {
        //
    }

    /**
     * Handle the Product "force deleted" event.
     */
    public function forceDeleted(Product $product): void
    {
        //
    }
}
