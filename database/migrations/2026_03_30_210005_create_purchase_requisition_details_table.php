<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Chi tiết yêu cầu mua (từng sản phẩm)
     */
    public function up(): void
    {
        Schema::create('purchase_requisition_details', function (Blueprint $table) {
            $table->id()->comment('ID chi tiết yêu cầu');
            $table->unsignedBigInteger('requisition_id')->comment('Khóa ngoại: Yêu cầu mua');
            $table->unsignedBigInteger('product_id')->comment('Khóa ngoại: Sản phẩm');
            $table->integer('line_number')->comment('Số thứ tự dòng');
            
            // Quantity
            $table->integer('quantity_requested')->comment('Số lượng yêu cầu');
            $table->decimal('estimated_unit_cost', 12, 4)->nullable()->comment('Ước tính giá/đơn vị');
            $table->decimal('estimated_line_total', 15, 2)->storedAs('quantity_requested * estimated_unit_cost')->comment('Tổng dòng ước tính');
            
            // Status
            $table->enum('status', ['pending', 'approved', 'ordered', 'received', 'cancelled'])->default('pending')->comment('Trạng thái dòng');
            
            // Tracking
            $table->integer('quantity_ordered')->default(0)->comment('Số lượng thực tế đã đặt');
            $table->integer('quantity_received')->default(0)->comment('Số lượng đã nhận');
            
            // Notes
            $table->text('notes')->nullable()->comment('Ghi chú (spec, chất lượng yêu cầu, etc)');
            $table->timestamps();

            // Khóa ngoại
            $table->foreign('requisition_id')
                ->references('id')->on('purchase_requisitions')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('product_id')
                ->references('id')->on('products')
                ->onDelete('restrict')
                ->onUpdate('cascade');

            // Chỉ mục
            $table->index('requisition_id');
            $table->index('product_id');
            $table->unique(['requisition_id', 'line_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_requisition_details');
    }
};
