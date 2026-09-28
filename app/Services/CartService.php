<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;

class CartService
{
    public const SESSION_KEY = 'cart';

    /**
     * @return array<int|string, array{product_id: int, quantity: int}>
     */
    public function items(): array
    {
        return Session::get(self::SESSION_KEY, []);
    }

    public function quantityFor(int $productId): int
    {
        return (int) ($this->items()[$productId]['quantity'] ?? 0);
    }

    public function count(): int
    {
        return (int) collect($this->items())->sum('quantity');
    }

    public function isEmpty(): bool
    {
        return $this->count() === 0;
    }

    /**
     * @return Collection<int, array{product: Product, quantity: int, line_total: float}>
     */
    public function lines(): Collection
    {
        $items = $this->items();

        if ($items === []) {
            return collect();
        }

        $products = Product::query()
            ->whereIn('id', array_keys($items))
            ->get()
            ->keyBy('id');

        return collect($items)
            ->map(function (array $item) use ($products) {
                $product = $products->get($item['product_id']);

                if (! $product) {
                    return null;
                }

                $quantity = (int) $item['quantity'];

                return [
                    'product' => $product,
                    'quantity' => $quantity,
                    'line_total' => (float) $product->price * $quantity,
                ];
            })
            ->filter()
            ->values();
    }

    public function subtotal(): float
    {
        return (float) $this->lines()->sum('line_total');
    }

    /**
     * @return array<int|string, array{product_id: int, quantity: int}>
     */
    public function add(Product $product, int $quantity = 1): array
    {
        return $this->write($product, $this->quantityFor($product->id) + $quantity);
    }

    /**
     * @return array<int|string, array{product_id: int, quantity: int}>
     */
    public function update(Product $product, int $quantity): array
    {
        if ($quantity <= 0) {
            return $this->remove($product->id);
        }

        return $this->write($product, $quantity);
    }

    /**
     * @return array<int|string, array{product_id: int, quantity: int}>
     */
    public function remove(int $productId): array
    {
        $cart = $this->items();
        unset($cart[$productId]);
        Session::put(self::SESSION_KEY, $cart);

        return $cart;
    }

    public function clear(): void
    {
        Session::forget(self::SESSION_KEY);
    }

    /**
     * @return array<int|string, array{product_id: int, quantity: int}>
     */
    private function write(Product $product, int $quantity): array
    {
        if ($quantity > $product->stock_quantity) {
            throw ValidationException::withMessages([
                'quantity' => 'Not enough stock available for this product.',
            ]);
        }

        $cart = $this->items();
        $cart[$product->id] = [
            'product_id' => $product->id,
            'quantity' => $quantity,
        ];

        Session::put(self::SESSION_KEY, $cart);

        return $cart;
    }
}
