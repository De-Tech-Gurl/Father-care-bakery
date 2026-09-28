<?php

namespace App\Repositories;

use App\Contracts\Repositories\ProductRepositoryInterface;
use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class ProductRepository extends BaseRepository implements ProductRepositoryInterface
{
    public function __construct(Product $model)
    {
        parent::__construct($model);
    }

    public function getFeatured(int $limit = 6): Collection
    {
        return $this->model->where('is_active', true)
            ->where('is_featured', true)
            ->limit($limit)
            ->get();
    }

    public function getLatest(int $limit = 6): Collection
    {
        return $this->model->where('is_active', true)
            ->latest()
            ->limit($limit)
            ->get();
    }

    public function getByCategorySlug(string $slug, int $limit = 6): Collection
    {
        return $this->model->whereHas('category', function ($query) use ($slug) {
            $query->where('slug', $slug)->where('is_active', true);
        })->where('is_active', true)->orderBy('price')->limit($limit)->get();
    }

    public function search(string $query): Collection
    {
        return $this->model->where('is_active', true)
            ->where(function ($builder) use ($query) {
                $builder->where('name', 'LIKE', "%{$query}%")
                    ->orWhere('description', 'LIKE', "%{$query}%");
            })
            ->get();
    }

    public function paginateCatalog(?int $categoryId, ?string $search, int $perPage = 12): LengthAwarePaginator
    {
        return $this->model->query()
            ->where('is_active', true)
            ->with('category')
            ->when($categoryId, fn ($query) => $query->where('category_id', $categoryId))
            ->when($search, function ($query) use ($search) {
                $query->where(function ($builder) use ($search) {
                    $builder->where('name', 'LIKE', "%{$search}%")
                        ->orWhere('description', 'LIKE', "%{$search}%");
                });
            })
            ->latest()
            ->paginate($perPage)
            ->withQueryString();
    }
}
