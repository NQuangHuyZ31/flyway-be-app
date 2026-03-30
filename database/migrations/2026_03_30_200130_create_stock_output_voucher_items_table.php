<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Chi tiết dòng sản phẩm trong phiếu xuất kho
     */
    public function up(): void
    {
        Schema::create('stock_out_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('stock_out_id')->comment('FK đến stock_output_vouchers');
            $table->unsignedBigInteger('product_id')->comment('Sản phẩm xuất');
            $table->unsignedBigInteger('batch_id')->nullable()->comment('Lô hàng cụ thể (FIFO/LIFO tracking)');
            $table->integer('line_number')->comment('Số thứ tự dòng');
            $table->integer('quantity_ordered')->comment('Số lượng dự kiến xuất');
            $table->integer('quantity_output')->default(0)->comment('Số lượng thực tế xuất');
            $table->decimal('unit_cost', 12, 2)->comment('Giá vốn/đơn vị của batch');
            $table->decimal('line_total', 15, 2)->comment('Tổng dòng = quantity_output * unit_cost');
            $table->text('notes')->nullable()->comment('Ghi chú dòng');
            $table->timestamps();

            // foreign key
            $table->foreign('stock_out_id')->references('id')->on('stock_outs')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('batch_id')->references('id')->on('product_batches')->onDelete('cascade');
            
            // Indexes
            $table->index('stock_out_id');
            $table->index('product_id');
            $table->index('batch_id');
            $table->unique(['stock_out_id', 'line_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_output_voucher_items');
    }
};
