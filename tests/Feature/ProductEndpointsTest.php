<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ProductEndpointsTest extends TestCase
{
    use RefreshDatabase;

    public function test_products_dashboard_displays_all_products_and_summary_values(): void
    {
        $category = Category::factory()->create(['name' => 'Accessories']);
        $inStock = Product::factory()->create([
            'category_id' => $category->id,
            'name' => 'Wireless Keyboard',
            'sku' => 'KEYBOARD-001',
            'price' => 50,
            'stock' => 8,
            'is_active' => true,
        ]);
        Product::factory()->create([
            'category_id' => $category->id,
            'name' => 'USB Hub',
            'sku' => 'HUB-001',
            'price' => 25,
            'stock' => 0,
            'is_active' => false,
        ]);
        Product::factory()->create([
            'price' => 2,
            'stock' => 50,
        ]);

        $response = $this->get(route('products.index'));

        $response->assertOk()
            ->assertSee('Wireless Keyboard')
            ->assertSee('KEYBOARD-001')
            ->assertSee('Accessories')
            ->assertSee('USB Hub')
            ->assertSee('Total products')
            ->assertSee('Low stock');

        $response->assertViewHas('products', function ($products) use ($inStock): bool {
            return $products->count() === 3
                && $products->firstWhere('id', $inStock->id)->relationLoaded('category');
        });
        $response->assertViewHas('totalProducts', 3);
        $response->assertViewHas('inStockProducts', 2);
        $response->assertViewHas('lowStockProducts', 1);
        $response->assertViewHas('inventoryValue', 500);
    }

    public function test_products_dashboard_displays_an_empty_state(): void
    {
        $this->get(route('products.index'))
            ->assertOk()
            ->assertSee('No products yet')
            ->assertViewHas('totalProducts', 0);
    }

    public function test_products_can_be_listed(): void
    {
        Product::factory()->count(2)->create();

        $response = $this->getJson('/api/products');

        $response->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'sku', 'name', 'price', 'category'],
                ],
                'meta' => ['total'],
            ]);
    }

    public function test_a_product_can_be_shown_with_its_category_and_suppliers(): void
    {
        $product = Product::factory()->create();
        $supplier = Supplier::factory()->create();
        $product->suppliers()->attach($supplier, [
            'cost_price' => 12.50,
            'supplier_sku' => 'SUP-123',
        ]);

        $response = $this->getJson("/api/products/{$product->id}");

        $response->assertOk()
            ->assertJsonPath('data.id', $product->id)
            ->assertJsonPath('data.category.id', $product->category_id)
            ->assertJsonPath('data.suppliers.0.id', $supplier->id);
    }

    public function test_an_authenticated_user_can_create_a_product_and_attach_suppliers(): void
    {
        Sanctum::actingAs(User::factory()->create());
        $category = Category::factory()->create();
        $supplier = Supplier::factory()->create();

        $response = $this->postJson('/api/products', [
            'category_id' => $category->id,
            'name' => 'Wireless Keyboard',
            'sku' => 'KEYBOARD-001',
            'description' => 'Compact wireless keyboard',
            'price' => 49.99,
            'stock' => 25,
            'is_active' => true,
            'supplier_ids' => [$supplier->id],
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.name', 'Wireless Keyboard')
            ->assertJsonPath('data.suppliers.0.id', $supplier->id);

        $this->assertDatabaseHas('products', [
            'sku' => 'KEYBOARD-001',
            'category_id' => $category->id,
        ]);
        $this->assertDatabaseHas('product_supplier', [
            'product_id' => $response->json('data.id'),
            'supplier_id' => $supplier->id,
        ]);
    }

    public function test_an_authenticated_user_can_update_a_product_and_sync_suppliers(): void
    {
        Sanctum::actingAs(User::factory()->create());
        $product = Product::factory()->create();
        $oldSupplier = Supplier::factory()->create();
        $newSupplier = Supplier::factory()->create();
        $product->suppliers()->attach($oldSupplier);

        $response = $this->putJson("/api/products/{$product->id}", [
            'name' => 'Updated Product',
            'supplier_ids' => [$newSupplier->id],
        ]);

        $response->assertOk()
            ->assertJsonPath('data.name', 'Updated Product')
            ->assertJsonPath('data.suppliers.0.id', $newSupplier->id);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Updated Product',
        ]);
        $this->assertDatabaseMissing('product_supplier', [
            'product_id' => $product->id,
            'supplier_id' => $oldSupplier->id,
        ]);
        $this->assertDatabaseHas('product_supplier', [
            'product_id' => $product->id,
            'supplier_id' => $newSupplier->id,
        ]);
    }

    public function test_an_authenticated_user_can_soft_delete_a_product(): void
    {
        Sanctum::actingAs(User::factory()->create());
        $product = Product::factory()->create();

        $response = $this->deleteJson("/api/products/{$product->id}");

        $response->assertOk()
            ->assertJson(['message' => 'Product deleted successfully.']);
        $this->assertSoftDeleted('products', ['id' => $product->id]);
    }
}
