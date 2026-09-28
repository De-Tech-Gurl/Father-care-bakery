<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\AddToCartRequest;
use App\Http\Requests\Customer\UpdateCartRequest;
use App\Models\Product;
use App\Services\CartService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CartController extends Controller
{
    public function __construct(protected CartService $cart) {}

    public function index(): View
    {
        return view('customer.cart.index', [
            'lines' => $this->cart->lines(),
            'subtotal' => $this->cart->subtotal(),
        ]);
    }

    public function add(AddToCartRequest $request): JsonResponse
    {
        $product = Product::query()->findOrFail($request->productId());

        $this->cart->add($product, $request->integer('quantity'));

        return response()->json([
            'message' => 'Added to cart.',
            'cart_count' => $this->cart->count(),
        ]);
    }

    public function update(UpdateCartRequest $request, Product $product): RedirectResponse|JsonResponse
    {
        $this->cart->update($product, $request->quantity());

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Cart updated.',
                'cart_count' => $this->cart->count(),
                'subtotal' => $this->cart->subtotal(),
                'quantity' => $this->cart->quantityFor($product->id),
                'product_id' => $product->id,
            ]);
        }

        return back()->with('success', 'Cart updated.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $this->cart->remove($product->id);

        return back()->with('success', 'Item removed from cart.');
    }
}
