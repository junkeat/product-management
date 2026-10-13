<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

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
}
