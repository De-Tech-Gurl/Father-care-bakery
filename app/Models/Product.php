<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'price',
        'compare_at_price',
        'cost_price',
        'image',
        'stock_quantity',
        'low_stock_threshold',
        'is_active',
        'is_featured',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'compare_at_price' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function inventoryMovements(): HasMany
    {
        return $this->hasMany(InventoryMovement::class);
    }

    public function isLowStock(): bool
    {
        return $this->stock_quantity <= $this->low_stock_threshold;
    }

    /**
     * @return Attribute<string|null, never>
     */
    protected function imageUrl(): Attribute
    {
        return Attribute::get(function (): ?string {
            if (! filled($this->image)) {
                return null;
            }

            return '/storage/'.ltrim((string) $this->image, '/');
        });
    }

    /**
     * @return Attribute<list<string>, never>
     */
    protected function descriptionLines(): Attribute
    {
        return Attribute::get(function (): array {
            return collect(preg_split('/\R/u', (string) $this->description) ?: [])
                ->map(fn (string $line): string => trim($line))
                ->filter()
                ->values()
                ->all();
        });
    }

    /**
     * @return Attribute<int|null, never>
     */
    protected function savingsPercent(): Attribute
    {
        return Attribute::get(function (): ?int {
            if ($this->compare_at_price === null || $this->compare_at_price <= $this->price) {
                return null;
            }

            return (int) round((($this->compare_at_price - $this->price) / $this->compare_at_price) * 100);
        });
    }

    public function getProfitMarginAttribute(): ?float
    {
        if ($this->cost_price && $this->cost_price > 0) {
            return (($this->price - $this->cost_price) / $this->cost_price) * 100;
        }

        return null;
    }
}
