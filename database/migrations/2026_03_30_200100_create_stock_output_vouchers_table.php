<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tạo bảng phiếu xuất kho (Stock Output Vouchers)
     * Dùng để quản lý việc xuất hàng cho bán, chuyển kho, hàng tặng, etc.
     */
    public function up(): void
    {
        Schema::create('stock_outs', function (Blueprint $table) {
            $table->id();
			$table->string('name');
            $table->string('voucher_code', 100)->unique()->comment('Mã phiếu xuất kho (VD: XK-2026-001)');
            $table->enum('output_type', ['sales_order', 'transfer_out', 'sample', 'damage', 'loss', 'return_to_supplier', 'adjustment'])->comment('Loại xuất (bán, chuyển kho, mẫu, hỏng, mất, trả supplier, điều chỉnh)');
            $table->unsignedBigInteger('customer_id')->nullable()->comment('Khách hàng (nếu bán)');
            $table->unsignedBigInteger('warehouse_id')->comment('Kho xuất hàng');
            $table->unsignedBigInteger('section_id')->nullable()->comment('Khu vực xuất cụ thể');
            $table->unsignedBigInteger('order_id')->nullable()->comment('Liên kết với orders table (sales_order)');
            $table->date('output_date')->comment('Ngày xuất');
            $table->unsignedBigInteger('created_by')->comment('Người tạo phiếu');
            $table->unsignedBigInteger('approved_by')->nullable()->comment('Người duyệt phiếu');
            $table->timestamp('approved_at')->nullable()->comment('Thời gian duyệt');
            $table->unsignedBigInteger('output_by')->nullable()->comment('Người thực hiện xuất hàng (kho)');
            $table->timestamp('output_at')->nullable()->comment('Thời gian xuất');
            $table->unsignedBigInteger('status_id')->nullable()->comment('Trạng thái phiếu (liên kết voucher_statuses)');   
            $table->decimal('total_quantity', 15, 2)->default(0)->comment('Tổng số lượng xuất');
            $table->decimal('total_cost', 15, 2)->nullable()->comment('Tổng giá vốn xuất');
            $table->text('notes')->nullable()->comment('Ghi chú về phiếu xuất');
            $table->text('rejection_reason')->nullable()->comment('Lý do từ chối (nếu status = cancelled)');
            $table->softDeletes();
            $table->timestamps();

            // foregin key
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->foreign('warehouse_id')->references('id')->on('warehouses')->onDelete('cascade');
            $table->foreign('section_id')->references('id')->on('warehouse_sections')->onDelete('cascade');
            $table->foreign('status_id')->references('id')->on('stock_in_out_status')->onDelete('cascade');

            // Indexes
            $table->index('name');
            $table->index('warehouse_id');
            $table->index('customer_id');
            $table->index('output_date');
            $table->index('status_id');
            $table->index('created_by');
            $table->index(['output_date', 'warehouse_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_output_vouchers');
    }
};
