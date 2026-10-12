<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Builder;

class Product extends Model
{
    /** @use HasFactory<\Database\Factories\ProductFactory> */
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'sku',
        'description',
        'price',
        'stock',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'stock' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function suppliers(): BelongsToMany
    {
        return $this->belongsToMany(Supplier::class)
            ->withPivot(['cost_price', 'supplier_sku'])
            ->withTimestamps();
    }

    public function scopeFilter(Builder $query, array $filters): Builder
    {
        $lowStockThreshold = 10;

        return $query
            ->when(
                $filters['category_id'] ?? null,
                fn($q, $id) => $q->where('category_id', $id)
            )
            ->when(
                isset($filters['min_price']),
                fn($q) => $q->where('price', '>=', $filters['min_price'])
            )
            ->when(
                isset($filters['max_price']),
                fn($q) => $q->where('price', '<=', $filters['max_price'])
            )
            ->when(
                isset($filters['min_stock']),
                fn($q) => $q->where('stock', '>=', $filters['min_stock'])
            )
            ->when(
                isset($filters['max_stock']),
                fn($q) => $q->where('stock', '<=', $filters['max_stock'])
            )
            ->when($filters['stock_status'] ?? null, fn($q, $status) => match ($status) {
                'in_stock'     => $q->where('stock', '>', 0),
                'out_of_stock' => $q->where('stock', 0),
                'low_stock'    => $q->whereBetween('stock', [1, $lowStockThreshold]),
            })
            ->when(
                isset($filters['is_active']),
                fn($q) => $q->where('is_active', $filters['is_active'])
            )
            ->when($filters['search'] ?? null, fn($q, $term) => $q->where(
                fn($w) => $w->where('name', 'like', "%{$term}%")
                    ->orWhere('sku', 'like', "%{$term}%")
            ));
    }
}
