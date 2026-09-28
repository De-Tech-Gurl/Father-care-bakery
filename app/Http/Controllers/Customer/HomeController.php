<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Services\ProductService;

class HomeController extends Controller
{
    protected ProductService $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function index()
    {
        $featured = $this->productService->getFeatured(6);
        $latest = $this->productService->getLatest(8);
        $categories = Category::where('is_active', true)->get();
        $combosCategory = Category::query()
            ->where('slug', 'combos')
            ->where('is_active', true)
            ->first();
        $combos = $this->productService->getByCategorySlug('combos', 6);

        return view('customer.home.index', compact('featured', 'latest', 'categories', 'combos', 'combosCategory'));
    }
}
