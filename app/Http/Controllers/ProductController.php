<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;

class ProductController extends Controller
{
    public function index(): View
    {
        /** @var Collection<int, Product> $products */
        $products = Product::query()
            ->with('category')
            ->orderBy('name')
            ->get();

        return view('products.index', [
            'products' => $products,
            'totalProducts' => $products->count(),
            'inStockProducts' => $products->filter(fn (Product $product) => $product->stock > 0)->count(),
            'lowStockProducts' => $products->filter(
                fn (Product $product) => $product->stock > 0 && $product->stock <= 10
            )->count(),
            'inventoryValue' => $products->sum(
                fn (Product $product) => (float) $product->price * $product->stock
            ),
        ]);
    }
}
