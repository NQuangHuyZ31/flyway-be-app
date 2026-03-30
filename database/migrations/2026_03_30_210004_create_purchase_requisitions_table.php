<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Yêu cầu mua hàng (Purchase Requisition)
     * = Bước đầu trong process mua:
     * Department/Warehouse → Requisition → Approved → Purchase Order
     */
    public function up(): void
    {
        Schema::create('purchase_requisitions', function (Blueprint $table) {
            $table->id()->comment('ID yêu cầu mua');
            $table->string('requisition_code', 100)->unique()->comment('Mã yêu cầu mua (YC-2026-001)');
            $table->unsignedBigInteger('requested_by')->comment('Khóa ngoại: Người yêu cầu');
            $table->unsignedBigInteger('warehouse_id')->comment('Khóa ngoại: Kho cần hàng');
            
            // Funding
            $table->string('cost_center', 50)->nullable()->comment('Mã tâm chi phí');
            $table->string('project_code', 50)->nullable()->comment('Mã dự án (nếu liên quan)');
            
            // Timeline
            $table->date('request_date')->index()->comment('Ngày yêu cầu');
            $table->date('needed_by_date')->nullable()->comment('Ngày cần có hàng');
            $table->date('due_date')->nullable()->comment('Deadline phê duyệt');
            
            // Approvals
            $table->unsignedBigInteger('approved_by')->nullable()->comment('Khóa ngoại: Người phê duyệt');
            $table->timestamp('approved_at')->nullable()->comment('Thời gian phê duyệt');
            $table->text('approval_notes')->nullable()->comment('Ghi chú phê duyệt');
            
            // Status
            $table->enum('status', ['draft', 'submitted', 'approved', 'rejected', 'ordered', 'received', 'cancelled'])->default('draft')->index()->comment('Trạng thái');
            
            // Reference to Purchase Order (if any)
            $table->unsignedBigInteger('order_id')->nullable()->comment('Khóa ngoại: Đơn mua tương ứng');
            
            // Financial summary
            $table->decimal('estimated_total', 15, 2)->default(0)->comment('Ước tính tổng giá');
            $table->integer('total_items')->default(0)->comment('Tổng số dòng');
            
            // Additional info
            $table->text('purpose')->nullable()->comment('Mục đích mua');
            $table->text('special_requirements')->nullable()->comment('Yêu cầu đặc biệt');
            $table->text('rejection_reason')->nullable()->comment('Lý do từ chối (nếu rejected)');
			$table->softDeletes();
            $table->timestamps();

            // Khóa ngoại
            $table->foreign('requested_by')
                ->references('id')->on('users')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('warehouse_id')
                ->references('id')->on('warehouses')
                ->onDelete('restrict')
                ->onUpdate('cascade');

            $table->foreign('approved_by')
                ->references('id')->on('users')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('order_id')
                ->references('id')->on('orders')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            // Chỉ mục
            $table->index('requested_by');
            $table->index('warehouse_id');
            $table->index('approved_by');
            $table->index(['warehouse_id', 'status']);  // Tìm yêu cầu theo kho
            $table->index(['requested_by', 'status']);  // Tìm yêu cầu theo người
            $table->index('due_date');                   // Tracking deadline
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_requisitions');
    }
};
