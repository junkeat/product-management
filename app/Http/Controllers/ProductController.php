<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateProductRequest;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'status' => ['nullable', 'in:active,inactive'],
        ]);

        $products = Product::query()
            ->with('category')
            ->when(
                $filters['status'] ?? null,
                fn ($query, $status) => $query->where('is_active', $status === 'active')
            )
            ->orderBy('name')
            ->paginate(5)
            ->withQueryString();

        $summary = Product::query()
            ->selectRaw('
                COUNT(*) as total_products,
                SUM(CASE WHEN stock > 0 THEN 1 ELSE 0 END) as in_stock_products,
                SUM(CASE WHEN stock BETWEEN 1 AND 10 THEN 1 ELSE 0 END) as low_stock_products,
                COALESCE(SUM(price * stock), 0) as inventory_value
            ')
            ->first();

        return view('products.index', [
            'products' => $products,
            'status' => $filters['status'] ?? '',
            'totalProducts' => $summary->total_products,
            'inStockProducts' => $summary->in_stock_products,
            'lowStockProducts' => $summary->low_stock_products,
            'inventoryValue' => $summary->inventory_value,
        ]);
    }

    public function edit(Product $product): View
    {
        return view('products.edit', [
            'product' => $product,
            'categories' => Category::query()->orderBy('name')->get(),
        ]);
    }

    public function update(UpdateProductRequest $request, Product $product): RedirectResponse
    {
        $product->update($request->validated());

        return redirect()
            ->route('products.index')
            ->with('success', 'Product updated successfully.');
    }

    public function bulkDestroy(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['nullable', 'in:active,inactive'],
            'product_ids' => ['required', 'array', 'min:1'],
            'product_ids.*' => [
                'required',
                'integer',
                'distinct',
                Rule::exists('products', 'id')->whereNull('deleted_at'),
            ],
        ]);

        $products = Product::query()->whereKey($data['product_ids'])->get();
        $deletedCount = $products->count();

        $products->each(fn (Product $product) => $product->delete());

        return redirect()
            ->route('products.index', array_filter(['status' => $data['status'] ?? null]))
            ->with('success', "{$deletedCount} products deleted successfully.");
    }
}
