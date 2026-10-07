<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use App\Services\ProductService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function __construct(protected ProductService $products) {}

    public function index(Request $request): View
    {
        $products = $this->products->catalog(
            $this->resolveCategoryId($request->query('category')),
            $request->string('q')->toString() ?: null,
        );

        $categories = Category::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('customer.products.index', compact('products', 'categories'));
    }

    public function show(Request $request, Product $product): View
    {
        abort_unless($product->is_active, 404);

        $product->load('category')
            ->loadAvg('reviews', 'rating')
            ->loadCount('reviews');

        $reviews = $product->reviews()
            ->with('user')
            ->latest()
            ->paginate(10);

        $customer = $request->user();
        $myReview = $customer instanceof User && $customer->isCustomer()
            ? $product->reviews()->whereBelongsTo($customer, 'user')->first()
            : null;

        $related = Product::query()
            ->where('is_active', true)
            ->where('id', '!=', $product->id)
            ->when($product->category_id, fn ($query) => $query->where('category_id', $product->category_id))
            ->limit(4)
            ->get();

        return view('customer.products.show', compact('product', 'related', 'reviews', 'myReview'));
    }

    private function resolveCategoryId(mixed $category): ?int
    {
        if (! filled($category)) {
            return null;
        }

        if (is_numeric($category)) {
            return (int) $category;
        }

        return Category::query()
            ->where('is_active', true)
            ->where('slug', $category)
            ->value('id');
    }
}
