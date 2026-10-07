<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductReview;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ProductReviewController extends Controller
{
    public function store(Request $request, Product $product): RedirectResponse
    {
        abort_unless($product->is_active, 404);

        $customer = $request->user();
        abort_unless($customer instanceof User && $customer->isCustomer(), 403);

        $validated = $request->validate([
            'rating' => ['required', 'integer', 'between:1,5'],
            'comment' => ['required', 'string', 'max:2000'],
        ]);

        $review = ProductReview::query()->updateOrCreate(
            [
                'product_id' => $product->id,
                'user_id' => $customer->id,
            ],
            $validated,
        );

        $message = $review->wasRecentlyCreated
            ? 'Your review has been submitted.'
            : 'Your review has been updated.';

        return redirect()
            ->to(route('customer.products.show', $product).'#reviews')
            ->with('status', $message);
    }
}
