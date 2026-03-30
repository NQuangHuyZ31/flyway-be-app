<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tạo bảng phiếu nhập kho (Stock Input Vouchers)
     * Dùng để quản lý việc nhập hàng từ supplier hoặc nhiều nguồn khác
     */
    public function up(): void
    {
        Schema::create('stock_ins', function (Blueprint $table) {
            $table->id();
			$table->string('name');
            $table->string('voucher_code', 100)->unique()->comment('Mã phiếu nhập kho (VD: NHK-2026-001)');
            $table->enum('input_type', ['purchase_order', 'return_from_customer', 'transfer_in', 'adjustment', 'sample'])->comment('Loại nhập (mua, trả hàng, chuyển kho, điều chỉnh, mẫu)');
            $table->unsignedBigInteger('supplier_id')->nullable()->comment('Nhà cung cấp (nếu mua)');
            $table->unsignedBigInteger('warehouse_id')->comment('Kho nhận hàng');
            $table->unsignedBigInteger('section_id')->nullable()->comment('Khu vực nhận cụ thể');
            $table->unsignedBigInteger('order_id')->nullable()->comment('Liên kết với orders table (purchase_order)');
            $table->date('input_date')->comment('Ngày nhập');
            $table->string('invoice_number', 100)->nullable()->comment('Số hóa đơn/chứng từ kèm theo');
            $table->unsignedBigInteger('created_by')->comment('Người tạo phiếu');
            $table->unsignedBigInteger('approved_by')->nullable()->comment('Người duyệt phiếu');
            $table->timestamp('approved_at')->nullable()->comment('Thời gian duyệt');
            $table->unsignedBigInteger('received_by')->nullable()->comment('Người nhận hàng (kho)');
            $table->timestamp('received_at')->nullable()->comment('Thời gian nhận');
            $table->unsignedBigInteger('status_id')->nullable()->comment('Trạng thái phiếu (liên kết voucher_statuses)');
            $table->decimal('total_quantity', 15, 2)->default(0)->comment('Tổng số lượng nhập');
            $table->decimal('total_cost', 15, 2)->nullable()->comment('Tổng giá vốn');
            $table->text('notes')->nullable()->comment('Ghi chú về phiếu nhập');
            $table->text('rejection_reason')->nullable()->comment('Lý do từ chối (nếu status = rejected)');
            $table->softDeletes();
            $table->timestamps();

            // foregin key
            $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('cascade');
            $table->foreign('warehouse_id')->references('id')->on('warehouses')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('cascade');   
            $table->foreign('status_id')->references('id')->on('stock_in_out_status')->onDelete('cascade'); 

            // Indexes
            $table->index('name');
            $table->index('warehouse_id');
            $table->index('supplier_id');
            $table->index('input_date');
            $table->index('status_id');
            $table->index('created_by');
            $table->index(['input_date', 'warehouse_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_input_vouchers');
    }
};
