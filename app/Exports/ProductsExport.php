<?php

namespace App\Exports;

use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ProductsExport implements FromQuery, ShouldAutoSize, WithHeadings, WithMapping
{
    public function __construct(
        private readonly ?string $status = null,
    ) {}

    public function query(): Builder
    {
        return Product::query()
            ->with('category')
            ->when(
                $this->status,
                fn (Builder $query, string $status) => $query->where('is_active', $status)
            )
            ->orderBy('name');
    }

    public function headings(): array
    {
        return ['ID', 'Name', 'SKU', 'Category', 'Description', 'Price', 'Stock', 'Status'];
    }

    /**
     * @param  Product  $product
     * @return array<int, mixed>
     */
    public function map($product): array
    {
        return [
            $product->id,
            $product->name,
            $product->sku,
            $product->category->name,
            $product->description,
            (float) $product->price,
            $product->stock,
            $product->is_active ? 'Active' : 'Inactive',
        ];
    }
}
