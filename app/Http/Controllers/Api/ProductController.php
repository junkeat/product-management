<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\IndexProductRequest;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;

class ProductController extends Controller
{
    public function index(IndexProductRequest $request): AnonymousResourceCollection
    {
        $data = $request->validated();

        $products = Product::query()
            ->with('category')
            ->filter($data)
            ->orderBy($data['sort'] ?? 'created_at', $data['direction'] ?? 'desc')
            ->paginate($data['per_page'] ?? 15)
            ->withQueryString();

        return ProductResource::collection($products);
    }

    public function store(StoreProductRequest $request): JsonResponse
    {
        $data = $request->validated();
        $supplierIds = Arr::pull($data, 'supplier_ids');

        $product = Product::create($data);

        if ($supplierIds) {
            $product->suppliers()->sync($supplierIds);
        }

        return ProductResource::make($product->load(['category', 'suppliers']))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(Product $product): ProductResource
    {
        return ProductResource::make($product->load(['category', 'suppliers']));
    }

    public function update(UpdateProductRequest $request, Product $product): ProductResource
    {
        $data = $request->validated();
        $supplierIds = Arr::pull($data, 'supplier_ids');

        $product->update($data);

        if ($supplierIds !== null) {
            $product->suppliers()->sync($supplierIds);
        }

        return ProductResource::make($product->load(['category', 'suppliers']));
    }

    public function destroy(Product $product): Response
    {
        $product->delete();

        return response()->noContent();
    }
}
