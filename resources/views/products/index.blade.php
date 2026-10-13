<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Products · Product Management</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 font-sans text-slate-900 antialiased">
    <div class="min-h-screen lg:flex">
        <aside class="bg-slate-950 px-6 py-7 text-white lg:flex lg:w-64 lg:flex-col">
            <a href="{{ route('products.index') }}" class="flex items-center gap-3 text-lg font-semibold tracking-tight">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-500 text-sm font-bold">PM</span>
                Product Manager
            </a>

            <p class="mb-3 mt-10 text-xs font-semibold uppercase tracking-widest text-slate-500">Workspace</p>
            <nav aria-label="Main navigation">
                <a href="{{ route('products.index') }}" aria-current="page"
                    class="flex items-center gap-3 rounded-xl bg-slate-800 px-4 py-3 text-sm font-medium text-white">
                    <svg class="h-5 w-5 text-indigo-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 7.5 12 3l8 4.5v9L12 21l-8-4.5v-9Z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 7.75 7.5 4.5 7.5-4.5M12 12.5V21" />
                    </svg>
                    Products
                </a>
            </nav>
            <p class="mt-auto hidden pt-12 text-xs text-slate-500 lg:block">Inventory overview</p>
        </aside>

        <main class="min-w-0 flex-1 px-5 py-8 sm:px-8 lg:px-10">
            <div class="mx-auto max-w-7xl">
                <header class="mb-8 flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <p class="mb-2 text-sm font-medium text-indigo-600">Inventory</p>
                        <h1 class="text-3xl font-bold tracking-tight text-slate-950">Products</h1>
                        <p class="mt-2 text-sm text-slate-500">A complete overview of your product catalog.</p>
                    </div>
                    <span class="text-sm text-slate-500">{{ now()->format('F j, Y') }}</span>
                </header>

                <section class="mb-8 grid gap-4 sm:grid-cols-2 xl:grid-cols-4" aria-label="Product summary">
                    <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                        <p class="text-sm font-medium text-slate-500">Total products</p>
                        <p class="mt-3 text-3xl font-semibold tracking-tight">{{ number_format($totalProducts) }}</p>
                        <p class="mt-1 text-xs text-slate-400">In your catalog</p>
                    </article>
                    <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                        <p class="text-sm font-medium text-slate-500">In stock</p>
                        <p class="mt-3 text-3xl font-semibold tracking-tight">{{ number_format($inStockProducts) }}</p>
                        <p class="mt-1 text-xs text-slate-400">Products with available units</p>
                    </article>
                    <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                        <p class="text-sm font-medium text-slate-500">Low stock</p>
                        <p class="mt-3 text-3xl font-semibold tracking-tight">{{ number_format($lowStockProducts) }}</p>
                        <p class="mt-1 text-xs text-slate-400">10 units or fewer</p>
                    </article>
                    <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                        <p class="text-sm font-medium text-slate-500">Inventory value</p>
                        <p class="mt-3 text-3xl font-semibold tracking-tight">${{ number_format($inventoryValue, 2) }}</p>
                        <p class="mt-1 text-xs text-slate-400">Price × available units</p>
                    </article>
                </section>

                <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                    <div class="flex flex-col gap-1 border-b border-slate-200 px-6 py-5 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h2 class="text-lg font-semibold text-slate-900">All products</h2>
                        </div>
                        <form method="GET" action="{{ route('products.index') }}" class="flex flex-wrap items-center gap-2">
                            <label for="status" class="text-sm font-medium text-slate-600">Status</label>
                            <select id="status" name="status"
                                class="rounded-lg border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="" @selected($status === '')>All</option>
                                <option value="active" @selected($status === 'active')>Active</option>
                                <option value="inactive" @selected($status === 'inactive')>Inactive</option>
                            </select>
                            <button type="submit"
                                class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium transition hover:bg-indigo-700">
                                Apply
                            </button>
                        </form>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full min-w-[760px] text-left text-sm">
                            <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                                <tr>
                                    <th scope="col" class="px-6 py-4 font-semibold">Product</th>
                                    <th scope="col" class="px-6 py-4 font-semibold">Category</th>
                                    <th scope="col" class="px-6 py-4 font-semibold">Price</th>
                                    <th scope="col" class="px-6 py-4 font-semibold">Stock</th>
                                    <th scope="col" class="px-6 py-4 font-semibold">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse ($products as $product)
                                    <tr class="transition hover:bg-slate-50/70">
                                        <td class="px-6 py-4">
                                            <p class="font-semibold text-slate-900">{{ $product->name }}</p>
                                            <p class="mt-1 text-xs text-slate-500">SKU: {{ $product->sku }}</p>
                                        </td>
                                        <td class="px-6 py-4 text-slate-600">{{ $product->category->name }}</td>
                                        <td class="px-6 py-4 font-medium text-slate-900">${{ number_format((float) $product->price, 2) }}</td>
                                        <td class="px-6 py-4">
                                            <span class="font-medium text-slate-900">{{ number_format($product->stock) }}</span>
                                            <span class="ml-1 text-xs text-slate-500">
                                                @if ($product->stock === 0)
                                                    units · Out of stock
                                                @elseif ($product->stock <= 10)
                                                    units · Low
                                                @else
                                                    units
                                                @endif
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">
                                            @if ($product->is_active)
                                                <span class="inline-flex rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-medium text-emerald-700">Active</span>
                                            @else
                                                <span class="inline-flex rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-600">Inactive</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-16 text-center">
                                            <p class="font-semibold text-slate-900">No products yet</p>
                                            <p class="mt-1 text-sm text-slate-500">Products will appear here once they are added to your catalog.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if ($products->hasPages())
                        <div class="border-t border-slate-200 px-6 py-4">
                            {{ $products->links() }}
                        </div>
                    @endif
                </section>
            </div>
        </main>
    </div>
</body>
</html>
