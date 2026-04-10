<?php

namespace Tests\Feature;

use App\Models\Inventory;
use App\Models\Product;
use App\Models\Warehouse;
use App\Models\Category;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class InventoryControllerTest extends TestCase
{
    use RefreshDatabase;

    private $product;
    private $warehouse;

    protected function setUp(): void
    {
        parent::setUp();
        $this->product = Product::factory()->create(['category_id' => Category::factory()->create()->id]);
        $this->warehouse = Warehouse::factory()->create();
    }

    /**
     * Test getting all inventory
     */
    public function test_get_all_inventory()
    {
        Inventory::factory(5)->create([
            'product_id' => $this->product->id,
            'warehouse_id' => $this->warehouse->id,
        ]);

        $response = $this->getJson('/api/inventory');

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
     * Test creating inventory
     */
    public function test_create_inventory_successfully()
    {
        $data = [
            'product_id' => $this->product->id,
            'warehouse_id' => $this->warehouse->id,
            'quantity_on_hand' => 100,
            'quantity_reserved' => 10,
        ];

        $response = $this->postJson('/api/inventory', $data);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Tạo bản ghi kho hàng thành công',
            ]);

        $this->assertDatabaseHas('inventory', [
            'product_id' => $this->product->id,
            'warehouse_id' => $this->warehouse->id,
            'quantity_on_hand' => 100,
        ]);
    }

    /**
     * Test getting inventory by warehouse
     */
    public function test_get_inventory_by_warehouse()
    {
        Inventory::factory(3)->create([
            'product_id' => $this->product->id,
            'warehouse_id' => $this->warehouse->id,
        ]);

        $response = $this->getJson("/api/inventory/warehouse/{$this->warehouse->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);
    }

    /**
     * Test getting inventory by product
     */
    public function test_get_inventory_by_product()
    {
        Inventory::factory(3)->create([
            'product_id' => $this->product->id,
            'warehouse_id' => $this->warehouse->id,
        ]);

        $response = $this->getJson("/api/inventory/product/{$this->product->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);
    }

    /**
     * Test adjusting inventory
     */
    public function test_adjust_inventory_successfully()
    {
        $inventory = Inventory::factory()->create([
            'product_id' => $this->product->id,
            'warehouse_id' => $this->warehouse->id,
            'quantity_on_hand' => 100,
            'quantity_reserved' => 10,
        ]);

        $data = [
            'quantity_on_hand' => 150,
            'quantity_reserved' => 20,
        ];

        $response = $this->patchJson("/api/inventory/{$inventory->id}/adjust", $data);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Điều chỉnh kho hàng thành công',
            ]);

        $this->assertDatabaseHas('inventory', [
            'id' => $inventory->id,
            'quantity_on_hand' => 150,
        ]);
    }

    /**
     * Test validation for creating inventory
     */
    public function test_create_inventory_validation_fails()
    {
        $data = [
            'product_id' => 99999, // Non-existent product
            'warehouse_id' => 99999, // Non-existent warehouse
            'quantity_on_hand' => 'invalid', // Should be numeric
        ];

        $response = $this->postJson('/api/inventory', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'product_id',
                'warehouse_id',
                'quantity_on_hand',
            ]);
    }

    /**
     * Test getting non-existent inventory
     */
    public function test_get_non_existent_inventory()
    {
        $response = $this->getJson('/api/inventory/99999');

        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
            ]);
    }
}
