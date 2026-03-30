<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Chi tiết dòng sản phẩm trong phiếu nhập kho
     */
    public function up(): void
    {
        Schema::create('stock_in_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('stock_in_id')->comment('FK đến stock_input_vouchers');
            $table->unsignedBigInteger('product_id')->comment('Sản phẩm nhập');
            $table->unsignedBigInteger('batch_id')->nullable()->comment('Lô hàng (nếu là nhập từ purchase order)');
            $table->integer('line_number')->comment('Số thứ tự dòng');
            $table->integer('quantity_ordered')->comment('Số lượng dự kiến nhập');
            $table->integer('quantity_received')->default(0)->comment('Số lượng thực tế nhận');
            $table->integer('quantity_rejected')->default(0)->comment('Số lượng từ chối (lỗi, hỏng)');
            $table->decimal('unit_cost', 12, 2)->comment('Giá vốn/đơn vị');
            $table->decimal('line_total', 15, 2)->comment('Tổng dòng = quantity_received * unit_cost');
            $table->text('notes')->nullable()->comment('Ghi chú dòng (VD: ghi nhận lỗi, hỏng)');
            $table->text('rejection_notes')->nullable()->comment('Chi tiết lý do từ chối');
            $table->timestamps();

            // foreign key
            $table->foreign('stock_in_id')->references('id')->on('stock_ins')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('batch_id')->references('id')->on('product_batches')->onDelete('cascade');

            // Indexes
            $table->index('stock_in_id');
            $table->index('product_id');
            $table->index('batch_id');
            $table->unique(['stock_in_id', 'line_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_input_voucher_items');
    }
};
