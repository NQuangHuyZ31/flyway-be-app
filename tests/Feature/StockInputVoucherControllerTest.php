<?php

namespace Tests\Feature;

use App\Models\StockInputVoucher;
use App\Models\StockInputVoucherItem;
use App\Models\Supplier;
use App\Models\Warehouse;
use App\Models\Product;
use App\Models\User;
use App\Models\Category;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class StockInputVoucherControllerTest extends TestCase
{
    use RefreshDatabase;

    private $supplier;
    private $warehouse;
    private $product;
    private $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->supplier = Supplier::factory()->create();
        $this->warehouse = Warehouse::factory()->create();
        $this->product = Product::factory()->create(['category_id' => Category::factory()->create()->id]);
        $this->user = User::factory()->create();
    }

    /**
     * Test getting all stock input vouchers
     */
    public function test_get_all_vouchers()
    {
        StockInputVoucher::factory(5)->create([
            'supplier_id' => $this->supplier->id,
            'warehouse_id' => $this->warehouse->id,
            'created_by' => $this->user->id,
        ]);

        $response = $this->getJson('/api/stock-input-vouchers');

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
     * Test creating a stock input voucher
     */
    public function test_create_voucher_successfully()
    {
        $this->actingAs($this->user);

        $data = [
            'name' => 'Stock Input Voucher 001',
            'voucher_code' => 'SIV-2026-001',
            'input_type' => 'purchase_order',
            'supplier_id' => $this->supplier->id,
            'warehouse_id' => $this->warehouse->id,
            'input_date' => now()->toDateString(),
            'invoice_number' => 'INV-001',
            'notes' => 'Test import',
            'items' => [
                [
                    'product_id' => $this->product->id,
                    'quantity_ordered' => 100,
                    'unit_cost' => 50.00,
                ],
            ],
        ];

        $response = $this->postJson('/api/stock-input-vouchers', $data);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Tạo phiếu nhập kho thành công',
            ]);

        $this->assertDatabaseHas('stock_ins', [
            'voucher_code' => $data['voucher_code'],
            'supplier_id' => $this->supplier->id,
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

        $response = $this->postJson('/api/stock-input-vouchers', $data);

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
        StockInputVoucher::factory(3)->create([
            'supplier_id' => $this->supplier->id,
            'warehouse_id' => $this->warehouse->id,
            'created_by' => $this->user->id,
        ]);

        $response = $this->getJson("/api/stock-input-vouchers/warehouse/{$this->warehouse->id}");

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

        $voucher = StockInputVoucher::factory()->create([
            'supplier_id' => $this->supplier->id,
            'warehouse_id' => $this->warehouse->id,
            'created_by' => $this->user->id,
        ]);

        StockInputVoucherItem::factory()->create([
            'stock_in_id' => $voucher->id,
            'product_id' => $this->product->id,
        ]);

        $response = $this->postJson("/api/stock-input-vouchers/{$voucher->id}/submit");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Phiếu nhập kho đã gửi duyệt',
            ]);
    }

    /**
     * Test approving a voucher
     */
    public function test_approve_voucher_successfully()
    {
        $this->actingAs($this->user);

        $voucher = StockInputVoucher::factory()->create([
            'supplier_id' => $this->supplier->id,
            'warehouse_id' => $this->warehouse->id,
            'created_by' => $this->user->id,
        ]);

        $response = $this->postJson("/api/stock-input-vouchers/{$voucher->id}/approve");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Phiếu nhập kho đã được duyệt',
            ]);

        $this->assertDatabaseHas('stock_ins', [
            'id' => $voucher->id,
            'approved_by' => $this->user->id,
        ]);
    }

    /**
     * Test deleting a voucher
     */
    public function test_delete_voucher_successfully()
    {
        $this->actingAs($this->user);

        $voucher = StockInputVoucher::factory()->create([
            'supplier_id' => $this->supplier->id,
            'warehouse_id' => $this->warehouse->id,
            'created_by' => $this->user->id,
        ]);

        $response = $this->deleteJson("/api/stock-input-vouchers/{$voucher->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Xóa phiếu nhập kho thành công',
            ]);

        $this->assertSoftDeleted('stock_ins', ['id' => $voucher->id]);
    }

    /**
     * Test duplicate voucher code
     */
    public function test_create_voucher_with_duplicate_code()
    {
        $this->actingAs($this->user);

        $existingVoucher = StockInputVoucher::factory()->create([
            'supplier_id' => $this->supplier->id,
            'warehouse_id' => $this->warehouse->id,
            'created_by' => $this->user->id,
        ]);

        $data = [
            'name' => 'Another Voucher',
            'voucher_code' => $existingVoucher->voucher_code, // Duplicate code
            'input_type' => 'purchase_order',
            'supplier_id' => $this->supplier->id,
            'warehouse_id' => $this->warehouse->id,
            'input_date' => now()->toDateString(),
            'items' => [
                [
                    'product_id' => $this->product->id,
                    'quantity_ordered' => 100,
                    'unit_cost' => 50.00,
                ],
            ],
        ];

        $response = $this->postJson('/api/stock-input-vouchers', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['voucher_code']);
    }

    /**
     * Test getting non-existent voucher
     */
    public function test_get_non_existent_voucher()
    {
        $response = $this->getJson('/api/stock-input-vouchers/99999');

        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
            ]);
    }
}
