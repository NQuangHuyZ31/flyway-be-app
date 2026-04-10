<?php

namespace Tests\Feature;

use App\Models\Warehouse;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class WarehouseControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test getting all warehouses
     */
    public function test_get_all_warehouses()
    {
        Warehouse::factory(5)->create();

        $response = $this->getJson('/api/warehouses');

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
     * Test creating a warehouse
     */
    public function test_create_warehouse_successfully()
    {
        $data = [
            'warehouse_name' => 'Main Warehouse',
            'warehouse_code' => 'WH001',
            'location' => 'Ha Noi',
            'description' => 'Main distribution center',
            'is_active' => true,
        ];

        $response = $this->postJson('/api/warehouses', $data);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Tạo kho thành công',
            ]);

        $this->assertDatabaseHas('warehouses', [
            'warehouse_name' => $data['warehouse_name'],
            'warehouse_code' => $data['warehouse_code'],
        ]);
    }

    /**
     * Test getting active warehouses only
     */
    public function test_get_active_warehouses()
    {
        Warehouse::factory(3)->create(['is_active' => true]);
        Warehouse::factory(2)->create(['is_active' => false]);

        $response = $this->getJson('/api/warehouses/active');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);
    }

    /**
     * Test updating a warehouse
     */
    public function test_update_warehouse_successfully()
    {
        $warehouse = Warehouse::factory()->create();

        $data = [
            'warehouse_name' => 'Updated Warehouse Name',
            'is_active' => false,
        ];

        $response = $this->putJson("/api/warehouses/{$warehouse->id}", $data);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Cập nhật kho thành công',
            ]);

        $this->assertDatabaseHas('warehouses', [
            'id' => $warehouse->id,
            'warehouse_name' => $data['warehouse_name'],
        ]);
    }

    /**
     * Test deleting a warehouse
     */
    public function test_delete_warehouse_successfully()
    {
        $warehouse = Warehouse::factory()->create();

        $response = $this->deleteJson("/api/warehouses/{$warehouse->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Xóa kho thành công',
            ]);

        $this->assertSoftDeleted('warehouses', ['id' => $warehouse->id]);
    }

    /**
     * Test validation for creating warehouse
     */
    public function test_create_warehouse_validation_fails()
    {
        $data = [
            'warehouse_name' => '', // Required
            'warehouse_code' => '', // Required
            'location' => '', // Required
        ];

        $response = $this->postJson('/api/warehouses', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'warehouse_name',
                'warehouse_code',
                'location',
            ]);
    }

    /**
     * Test duplicate warehouse code
     */
    public function test_create_warehouse_with_duplicate_code()
    {
        $warehouse = Warehouse::factory()->create();

        $data = [
            'warehouse_name' => 'Another Warehouse',
            'warehouse_code' => $warehouse->warehouse_code, // Duplicate code
            'location' => 'Saigon',
        ];

        $response = $this->postJson('/api/warehouses', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['warehouse_code']);
    }

    /**
     * Test getting non-existent warehouse
     */
    public function test_get_non_existent_warehouse()
    {
        $response = $this->getJson('/api/warehouses/99999');

        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
            ]);
    }
}
