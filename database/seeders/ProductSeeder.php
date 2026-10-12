<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $categoryIds = Category::query()->pluck('id');
        $supplierIds = Supplier::query()->pluck('id');

        $products = Product::factory(10)->create([
            'category_id' => fn () => $categoryIds->random(),
        ]);

        $products->each(function (Product $product) use ($supplierIds): void {
            $product->suppliers()->sync(
                $supplierIds->mapWithKeys(fn (int $supplierId) => [
                    $supplierId => [
                        'cost_price' => fake()->randomFloat(2, 1, (float) $product->price),
                        'supplier_sku' => "{$product->sku}-{$supplierId}",
                    ],
                ])->all()
            );
        });
    }
}
