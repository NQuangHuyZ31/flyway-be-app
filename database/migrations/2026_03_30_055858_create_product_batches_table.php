<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tạo bảng lô hàng sản phẩm (Product Batches)
     * = Dùng để quản lý từng lô nhập hàng, theo dõi giá vốn FIFO/LIFO, hạn sử dụng
     */
    public function up(): void
    {
        Schema::create('product_batches', function (Blueprint $table) {
            $table->id()->comment('ID lô hàng');
            $table->unsignedBigInteger('product_id')->comment('Khóa ngoại: Sản phẩm');
            $table->string('batch_code', 100)->unique()->comment('Mã lô hàng duy nhất (VD: LH-2026-001)');
            $table->unsignedBigInteger('supplier_id')->nullable()->comment('Khóa ngoại: Nhà cung cấp (nullable nếu nhập từ nguồn khác)');
            $table->date('import_date')->index()->comment('Ngày nhập lô hàng');
            $table->integer('quantity_imported')->default(0)->comment('Số lượng nhập ban đầu');
            $table->integer('quantity_available')->default(0)->comment('Số lượng còn lại định kỳ');
            $table->decimal('unit_cost', 12, 4)->comment('Giá vốn/đơn vị (cho hóa toán COGS)');
            $table->decimal('unit_price', 12, 4)->comment('Giá bán kỳ vọng/đơn vị');
            $table->date('expiry_date')->nullable()->index()->comment('Ngày hết hạn (nullable nếu không có)');
            $table->enum('status', ['active', 'selling_out', 'expired', 'discontinued'])->default('active')->index()->comment('Trạng thái lô');
            $table->decimal('total_cost', 15, 2)->storedAs('quantity_available * unit_cost')->comment('Tổng giá vốn (calculated)');
            $table->text('notes')->nullable()->comment('Ghi chú lô hàng (chứng chỉ, chất lượng, vị trí lưu trữ)');
            $table->timestamps();

            // Khóa ngoại - INLINE như yêu cầu
            $table->foreign('product_id')
                ->references('id')->on('products')
                ->onDelete('cascade')       // Xóa sản phẩm → xóa lô hàng
                ->onUpdate('cascade');

            $table->foreign('supplier_id')
                ->references('id')->on('suppliers')
                ->onDelete('set null')      // Xóa supplier → set NULL (lô hàng vẫn tồn tại)
                ->onUpdate('cascade');

            // Chỉ mục cho truy vấn thường xuyên
            $table->index('product_id');
            $table->index(['product_id', 'import_date'], 'idx_product_import_date');  // FIFO lookup
            $table->index(['product_id', 'quantity_available'], 'idx_product_available');  // Available stock
            $table->index('supplier_id');
            $table->index('status', 'idx_product_batch_status_i');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_batches');
    }
};
