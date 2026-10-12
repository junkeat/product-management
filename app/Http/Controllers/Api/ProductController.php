<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\IndexProductRequest;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;

class ProductController extends Controller
{
    public function index(IndexProductRequest $request): JsonResponse
    {
        $data = $request->validated();

        $products = Product::query()
            ->with('category')
            ->filter($data)
            ->orderBy($data['sort'] ?? 'created_at', $data['direction'] ?? 'desc')
            ->paginate($data['per_page'] ?? 15)
            ->withQueryString();

        return ProductResource::collection($products)->response();
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

    public function show(Product $product): JsonResponse
    {
        return ProductResource::make($product->load(['category', 'suppliers']))->response();
    }

    public function update(UpdateProductRequest $request, Product $product): JsonResponse
    {
        $data = $request->validated();
        $supplierIds = Arr::pull($data, 'supplier_ids');

        $product->update($data);

        if ($supplierIds !== null) {
            $product->suppliers()->sync($supplierIds);
        }

        return ProductResource::make($product->load(['category', 'suppliers']))->response();
    }

    public function destroy(Product $product): JsonResponse
    {
        $product->delete();

        return response()->json(['message' => 'Product deleted successfully.']);
    }

    public function restore(Product $product): ProductResource
    {
        $product->restore();

        return new ProductResource($product);
    }

    public function forceDelete(Product $product): Response
    {
        $product->suppliers()->detach();
        $product->forceDelete();

        return response()->noContent();
    }
}
