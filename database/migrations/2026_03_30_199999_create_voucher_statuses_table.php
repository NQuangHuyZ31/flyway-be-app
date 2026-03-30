<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tạo bảng voucher_statuses
     * Định nghĩa tất cả các trạng thái có thể của phiếu
     * Giúp quản lí trạng thái linh hoạt, không cần sửa code khi cần thêm trạng thái
     */
    public function up(): void
    {
        Schema::create('stock_in_out_status', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique()->comment('Mã code (draft, pending_approval, approved, received)');
            $table->string('name', 100)->comment('Tên trạng thái hiển thị');
            $table->text('description')->nullable()->comment('Mô tả chi tiết trạng thái');
            $table->integer('order')->default(0)->comment('Thứ tự hiển thị');
            $table->enum('color', ['gray', 'blue', 'yellow', 'green', 'red', 'purple', 'orange'])->default('gray')->comment('Màu hiển thị UI');
            $table->boolean('is_active')->default(true)->comment('Trạng thái có hoạt động');
            $table->softDeletes();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('code');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('voucher_statuses');
    }
};
