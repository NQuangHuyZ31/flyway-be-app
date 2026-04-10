<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Category;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductControllerTest extends TestCase
{
    use RefreshDatabase;

    private $category;

    protected function setUp(): void
    {
        parent::setUp();
        $this->category = Category::factory()->create();
    }

    /**
     * Test getting all products
     */
    public function test_get_all_products()
    {
        Product::factory(5)->create(['category_id' => $this->category->id]);

        $response = $this->getJson('/api/products');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonStructure([
                'success',
                'data',
                'pagination',
            ]);
    }

    /**
     * Test creating a product
     */
    public function test_create_product_successfully()
    {
        $data = [
            'product_name' => 'Test Product',
            'product_code' => 'TP001',
            'sku' => 'SKU-TEST-001',
            'category_id' => $this->category->id,
            'unit_id' => 1,
            'price' => 100.00,
            'cost' => 50.00,
            'minimum_inventory' => 10,
        ];

        $response = $this->postJson('/api/products', $data);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Tạo sản phẩm thành công',
            ]);

        $this->assertDatabaseHas('products', [
            'product_name' => $data['product_name'],
            'sku' => $data['sku'],
        ]);
    }

    /**
     * Test creating product with invalid data
     */
    public function test_create_product_validation_fails()
    {
        $data = [
            'product_name' => '', // Required
            'product_code' => 'TP002',
            'sku' => 'SKU-TEST-002',
        ];

        $response = $this->postJson('/api/products', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'product_name',
                'category_id',
                'price',
                'cost',
            ]);
    }

    /**
     * Test getting a single product
     */
    public function test_get_single_product()
    {
        $product = Product::factory()->create(['category_id' => $this->category->id]);

        $response = $this->getJson("/api/products/{$product->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);
    }

    /**
     * Test updating a product
     */
    public function test_update_product_successfully()
    {
        $product = Product::factory()->create(['category_id' => $this->category->id]);

        $data = [
            'product_name' => 'Updated Product Name',
            'price' => 150.00,
        ];

        $response = $this->putJson("/api/products/{$product->id}", $data);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Cập nhật sản phẩm thành công',
            ]);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'product_name' => $data['product_name'],
        ]);
    }

    /**
     * Test deleting a product
     */
    public function test_delete_product_successfully()
    {
        $product = Product::factory()->create(['category_id' => $this->category->id]);

        $response = $this->deleteJson("/api/products/{$product->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Xóa sản phẩm thành công',
            ]);

        $this->assertSoftDeleted('products', ['id' => $product->id]);
    }

    /**
     * Test getting non-existent product
     */
    public function test_get_non_existent_product()
    {
        $response = $this->getJson('/api/products/99999');

        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
            ]);
    }

    /**
     * Test deleting non-existent product
     */
    public function test_delete_non_existent_product()
    {
        $response = $this->deleteJson('/api/products/99999');

        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
            ]);
    }

    /**
     * Test duplicate SKU validation
     */
    public function test_create_product_with_duplicate_sku()
    {
        $product = Product::factory()->create(['category_id' => $this->category->id]);

        $data = [
            'product_name' => 'Another Product',
            'product_code' => 'AP001',
            'sku' => $product->sku, // Duplicate SKU
            'category_id' => $this->category->id,
            'unit_id' => 1,
            'price' => 100.00,
            'cost' => 50.00,
            'minimum_inventory' => 10,
        ];

        $response = $this->postJson('/api/products', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['sku']);
    }
}
