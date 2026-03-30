<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Lịch sử giá vốn sản phẩm (Product Cost History)
     * = Lưu trữ mỗi lần giá vốn (unit cost) thay đổi
     * = Dùng để:
     * - Tính COGS chính xác (FIFO/LIFO)
     * - Audit trail cho price changes
     * - Analyze cost trends
     * - Revalue inventory
     */
    public function up(): void
    {
        Schema::create('product_cost_history', function (Blueprint $table) {
            $table->id()->comment('ID lịch sử giá vốn');
            $table->unsignedBigInteger('product_id')->comment('Khóa ngoại: Sản phẩm');
            
            // Old vs New cost
            $table->decimal('old_cost', 12, 4)->nullable()->comment('Giá vốn cũ');
            $table->decimal('new_cost', 12, 4)->comment('Giá vốn mới');
            $table->decimal('cost_change', 12, 4)->storedAs('new_cost - old_cost')->comment('Thay đổi giá vốn');
            
            // Reason for change
            $table->enum('change_reason', ['supplier_price_change', 'inflation_adjustment', 'bulk_discount', 'quality_assessment', 'revaluation', 'correction', 'other'])->comment('Lý do thay đổi');
            $table->text('details')->nullable()->comment('Chi tiết lý do');
            
            // Financial impact
            $table->enum('impact_scope', ['new_batches_only', 'apply_to_existing_inventory', 'apply_to_future_only'])->default('new_batches_only')->comment('Phạm vi áp dụng');
            
            // Change authorization
            $table->unsignedBigInteger('changed_by')->comment('Khóa ngoại: Người thay đổi');
            $table->unsignedBigInteger('approved_by')->nullable()->comment('Khóa ngoại: Người duyệt');
            $table->timestamp('approved_at')->nullable()->comment('Thời gian phê duyệt');
            $table->timestamp('effective_date')->nullable()->comment('Ngày có hiệu lực');
            
            // Status
            $table->enum('status', ['pending_approval', 'approved', 'applied', 'reversed', 'cancelled'])->default('pending_approval')->index()->comment('Trạng thái');
            
            // Reference
            $table->unsignedBigInteger('related_batch_id')->nullable()->comment('Khóa ngoại: Lô hàng liên quan (nếu dùng cho batch cụ thể)');
            
            // Auto-reversable
            $table->unsignedBigInteger('reversed_by')->nullable()->comment('Khóa ngoại: Người đảo ngược thay đổi');
            $table->timestamp('reversed_at')->nullable()->comment('Thời gian đảo ngược');
            $table->text('reversal_reason')->nullable()->comment('Lý do đảo ngược');
            
            $table->timestamps();

            // Khóa ngoại
            $table->foreign('product_id')
                ->references('id')->on('products')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('changed_by')
                ->references('id')->on('users')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('approved_by')
                ->references('id')->on('users')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('reversed_by')
                ->references('id')->on('users')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            // Chỉ mục
            $table->index('product_id');
            $table->index('changed_by');
            $table->index('approved_by');
            $table->index('status', 'idx_status');
            $table->index('created_at');
            $table->index(['product_id', 'created_at'],'idx_product_created_at'); // Lịch sử giá vốn sản phẩm
            $table->index(['product_id', 'effective_date'], 'idx_product_effective_date'); // Tìm giá vốn có hiệu lực
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_cost_history');
    }
};
