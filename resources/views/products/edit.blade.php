<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Update Product · Product Management</title>
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
                    Products
                </a>
            </nav>
            <p class="mt-auto hidden pt-12 text-xs text-slate-500 lg:block">Inventory overview</p>
        </aside>

        <main class="min-w-0 flex-1 px-5 py-8 sm:px-8 lg:px-10">
            <div class="mx-auto max-w-3xl">
                <header class="mb-8">
                    <a href="{{ route('products.index') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-700">
                        &larr; Back to products
                    </a>
                    <p class="mb-2 mt-6 text-sm font-medium text-indigo-600">Inventory</p>
                    <h1 class="text-3xl font-bold tracking-tight text-slate-950">Update Product</h1>
                    <p class="mt-2 text-sm text-slate-500">Edit the details for {{ $product->name }}.</p>
                </header>

                <form method="POST" action="{{ route('products.update', $product) }}"
                    class="space-y-6 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
                    @csrf
                    @method('PUT')

                    @if ($errors->any())
                        <div role="alert" class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">
                            <p class="font-semibold">Please correct the following fields:</p>
                            <ul class="mt-2 list-inside list-disc space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="grid gap-6 sm:grid-cols-2">
                        <div class="sm:col-span-2">
                            <label for="name" class="mb-2 block text-sm font-medium text-slate-700">Product name</label>
                            <input id="name" name="name" type="text" value="{{ old('name', $product->name) }}" required
                                class="w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @error('name')
                                <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="sku" class="mb-2 block text-sm font-medium text-slate-700">SKU</label>
                            <input id="sku" name="sku" type="text" value="{{ old('sku', $product->sku) }}" required
                                class="w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @error('sku')
                                <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="category_id" class="mb-2 block text-sm font-medium text-slate-700">Category</label>
                            <select id="category_id" name="category_id" required
                                class="w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" @selected((string) old('category_id', $product->category_id) === (string) $category->id)>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="price" class="mb-2 block text-sm font-medium text-slate-700">Price</label>
                            <input id="price" name="price" type="number" min="0" max="99999999.99" step="0.01"
                                value="{{ old('price', $product->price) }}" required
                                class="w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @error('price')
                                <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="stock" class="mb-2 block text-sm font-medium text-slate-700">Stock quantity</label>
                            <input id="stock" name="stock" type="number" min="0" step="1"
                                value="{{ old('stock', $product->stock) }}" required
                                class="w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @error('stock')
                                <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="is_active" class="mb-2 block text-sm font-medium text-slate-700">Status</label>
                            <select id="is_active" name="is_active" required
                                class="w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="1" @selected((string) old('is_active', (int) $product->is_active) === '1')>Active</option>
                                <option value="0" @selected((string) old('is_active', (int) $product->is_active) === '0')>Inactive</option>
                            </select>
                            @error('is_active')
                                <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="sm:col-span-2">
                            <label for="description" class="mb-2 block text-sm font-medium text-slate-700">Description</label>
                            <textarea id="description" name="description" rows="4"
                                class="w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('description', $product->description) }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="flex flex-col-reverse gap-3 border-t border-slate-100 pt-6 sm:flex-row sm:justify-end">
                        <a href="{{ route('products.index') }}"
                            class="rounded-lg border border-slate-300 px-4 py-2.5 text-center text-sm font-medium text-slate-700 transition hover:bg-slate-50">
                            Cancel
                        </a>
                        <button type="submit"
                            class="rounded-lg border border-slate-300 bg-indigo-600 px-5 py-2.5 text-sm font-medium transition hover:bg-indigo-700">
                            Save
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </div>
</body>
</html>
