<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('warehouses', function (Blueprint $table) {
            $table->id();
            $table->string('warehouse_name', 255)->comment('Tên kho');
            $table->string('warehouse_code', 50)->unique()->comment('Mã kho');
            $table->text('address')->comment('Địa chỉ kho');
            $table->string('city', 100)->comment('Thành phố');
            $table->string('district', 100)->nullable()->comment('Quận/Huyện');
            $table->string('country', 100)->default('Vietnam')->comment('Quốc gia');
            $table->string('phone', 20)->nullable()->comment('Số điện thoại');
            $table->string('email', 100)->nullable()->comment('Email');
            $table->unsignedBigInteger('manager_id')->nullable()->comment('Khóa ngoại đến users (người quản lý)');
            $table->enum('warehouse_type', ['general', 'cold_storage', 'hazmat', 'distribution'])->default('general')->comment('Loại kho');
            $table->decimal('capacity', 12, 2)->nullable()->comment('Dung tích tối đa (M3)');
            $table->decimal('current_occupancy', 12, 2)->default(0)->comment('Dợc chộ hiện tại');
            $table->text('notes')->nullable()->comment('Ghi chú');
            $table->boolean('is_active')->default(true)->comment('Kho có hoạt động');
            $table->softDeletes();
            $table->timestamps();
            
            // Foreign keys added in add_all_foreign_keys migration
            
            // Index
            $table->index('warehouse_code');
            $table->index('manager_id');
            $table->index('warehouse_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warehouses');
    }
};
