<?php

namespace App\Services;

use App\Contracts\Repositories\ProductRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class ProductService extends BaseService
{
    protected ProductRepositoryInterface $productRepo;

    public function __construct(ProductRepositoryInterface $productRepo)
    {
        parent::__construct($productRepo);
        $this->productRepo = $productRepo;
    }

    public function getFeatured(int $limit = 6): Collection
    {
        return $this->productRepo->getFeatured($limit);
    }

    public function getLatest(int $limit = 6): Collection
    {
        return $this->productRepo->getLatest($limit);
    }

    public function getByCategorySlug(string $slug, int $limit = 6): Collection
    {
        return $this->productRepo->getByCategorySlug($slug, $limit);
    }

    public function searchProducts(string $query): Collection
    {
        return $this->productRepo->search($query);
    }

    public function catalog(?int $categoryId, ?string $search, int $perPage = 12): LengthAwarePaginator
    {
        return $this->productRepo->paginateCatalog($categoryId, $search, $perPage);
    }
}
