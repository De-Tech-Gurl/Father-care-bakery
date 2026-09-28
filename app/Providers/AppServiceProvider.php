<?php

namespace App\Providers;

use App\Contracts\Repositories\ProductRepositoryInterface;
use App\Repositories\ProductRepository;
use App\Services\CartService;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(ProductRepositoryInterface::class, ProductRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();

        View::composer('layouts.customer', function ($view) {
            $view->with('cartCount', app(CartService::class)->count());
        });
    }
}
