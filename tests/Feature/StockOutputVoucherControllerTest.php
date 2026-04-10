<?php

namespace Tests\Feature;

use App\Models\StockOutputVoucher;
use App\Models\StockOutputVoucherItem;
use App\Models\Customer;
use App\Models\Warehouse;
use App\Models\Product;
use App\Models\User;
use App\Models\Category;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class StockOutputVoucherControllerTest extends TestCase
{
    use RefreshDatabase;

    private $customer;
    private $warehouse;
    private $product;
    private $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->customer = Customer::factory()->create();
        $this->warehouse = Warehouse::factory()->create();
        $this->product = Product::factory()->create(['category_id' => Category::factory()->create()->id]);
        $this->user = User::factory()->create();
    }

    /**
     * Test getting all stock output vouchers
     */
    public function test_get_all_vouchers()
    {
        StockOutputVoucher::factory(5)->create([
            'customer_id' => $this->customer->id,
            'warehouse_id' => $this->warehouse->id,
            'created_by' => $this->user->id,
        ]);

        $response = $this->getJson('/api/stock-output-vouchers');

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
     * Test creating a stock output voucher
     */
    public function test_create_voucher_successfully()
    {
        $this->actingAs($this->user);

        $data = [
            'name' => 'Stock Output Voucher 001',
            'voucher_code' => 'SOV-2026-001',
            'output_type' => 'sale',
            'customer_id' => $this->customer->id,
            'warehouse_id' => $this->warehouse->id,
            'output_date' => now()->toDateString(),
            'invoice_number' => 'INV-OUT-001',
            'notes' => 'Test export',
            'items' => [
                [
                    'product_id' => $this->product->id,
                    'quantity_ordered' => 50,
                    'unit_cost' => 75.00,
                ],
            ],
        ];

        $response = $this->postJson('/api/stock-output-vouchers', $data);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Tạo phiếu xuất kho thành công',
            ]);

        $this->assertDatabaseHas('stock_outs', [
            'voucher_code' => $data['voucher_code'],
            'customer_id' => $this->customer->id,
        ]);
    }

    /**
     * Test validation for creating voucher
     */
    public function test_create_voucher_validation_fails()
    {
        $this->actingAs($this->user);

        $data = [
            'name' => '', // Required
            'voucher_code' => '', // Required
            'warehouse_id' => 99999, // Non-existent
            'items' => [], // At least one item required
        ];

        $response = $this->postJson('/api/stock-output-vouchers', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'name',
                'voucher_code',
                'warehouse_id',
                'items',
            ]);
    }

    /**
     * Test getting vouchers by warehouse
     */
    public function test_get_vouchers_by_warehouse()
    {
        StockOutputVoucher::factory(3)->create([
            'customer_id' => $this->customer->id,
            'warehouse_id' => $this->warehouse->id,
            'created_by' => $this->user->id,
        ]);

        $response = $this->getJson("/api/stock-output-vouchers/warehouse/{$this->warehouse->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);
    }

    /**
     * Test submitting a voucher
     */
    public function test_submit_voucher_successfully()
    {
        $this->actingAs($this->user);

        $voucher = StockOutputVoucher::factory()->create([
            'customer_id' => $this->customer->id,
            'warehouse_id' => $this->warehouse->id,
            'created_by' => $this->user->id,
        ]);

        StockOutputVoucherItem::factory()->create([
            'stock_out_id' => $voucher->id,
            'product_id' => $this->product->id,
        ]);

        $response = $this->postJson("/api/stock-output-vouchers/{$voucher->id}/submit");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Phiếu xuất kho đã gửi duyệt',
            ]);
    }

    /**
     * Test approving a voucher
     */
    public function test_approve_voucher_successfully()
    {
        $this->actingAs($this->user);

        $voucher = StockOutputVoucher::factory()->create([
            'customer_id' => $this->customer->id,
            'warehouse_id' => $this->warehouse->id,
            'created_by' => $this->user->id,
        ]);

        $response = $this->postJson("/api/stock-output-vouchers/{$voucher->id}/approve");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Phiếu xuất kho đã được duyệt',
            ]);

        $this->assertDatabaseHas('stock_outs', [
            'id' => $voucher->id,
            'approved_by' => $this->user->id,
        ]);
    }

    /**
     * Test completing a voucher
     */
    public function test_complete_voucher_successfully()
    {
        $this->actingAs($this->user);

        $voucher = StockOutputVoucher::factory()->create([
            'customer_id' => $this->customer->id,
            'warehouse_id' => $this->warehouse->id,
            'created_by' => $this->user->id,
            'approved_by' => $this->user->id,
            'approved_at' => now(),
        ]);

        $response = $this->postJson("/api/stock-output-vouchers/{$voucher->id}/complete");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Phiếu xuất kho đã hoàn thành',
            ]);

        $this->assertDatabaseHas('stock_outs', [
            'id' => $voucher->id,
            'completed_by' => $this->user->id,
        ]);
    }

    /**
     * Test deleting a voucher
     */
    public function test_delete_voucher_successfully()
    {
        $this->actingAs($this->user);

        $voucher = StockOutputVoucher::factory()->create([
            'customer_id' => $this->customer->id,
            'warehouse_id' => $this->warehouse->id,
            'created_by' => $this->user->id,
        ]);

        $response = $this->deleteJson("/api/stock-output-vouchers/{$voucher->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Xóa phiếu xuất kho thành công',
            ]);

        $this->assertSoftDeleted('stock_outs', ['id' => $voucher->id]);
    }

    /**
     * Test duplicate voucher code
     */
    public function test_create_voucher_with_duplicate_code()
    {
        $this->actingAs($this->user);

        $existingVoucher = StockOutputVoucher::factory()->create([
            'customer_id' => $this->customer->id,
            'warehouse_id' => $this->warehouse->id,
            'created_by' => $this->user->id,
        ]);

        $data = [
            'name' => 'Another Voucher',
            'voucher_code' => $existingVoucher->voucher_code, // Duplicate code
            'output_type' => 'sale',
            'customer_id' => $this->customer->id,
            'warehouse_id' => $this->warehouse->id,
            'output_date' => now()->toDateString(),
            'items' => [
                [
                    'product_id' => $this->product->id,
                    'quantity_ordered' => 50,
                    'unit_cost' => 75.00,
                ],
            ],
        ];

        $response = $this->postJson('/api/stock-output-vouchers', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['voucher_code']);
    }

    /**
     * Test getting non-existent voucher
     */
    public function test_get_non_existent_voucher()
    {
        $response = $this->getJson('/api/stock-output-vouchers/99999');

        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
            ]);
    }
}
