<?php

namespace App\Contracts\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface ProductRepositoryInterface extends BaseRepositoryInterface
{
    public function getFeatured(int $limit = 6): Collection;

    public function getLatest(int $limit = 6): Collection;

    public function getByCategorySlug(string $slug, int $limit = 6): Collection;

    public function search(string $query): Collection;

    public function paginateCatalog(?int $categoryId, ?string $search, int $perPage = 12): LengthAwarePaginator;
}
