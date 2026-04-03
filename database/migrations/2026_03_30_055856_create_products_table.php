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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('product_name', 255)->comment('Tên sản phẩm');
            $table->string('product_code', 50)->unique()->comment('Mã sản phẩm');
            $table->string('sku', 100)->unique()->comment('SKU - Mã chỈ đó');
            $table->unsignedBigInteger('category_id')->nullable()->comment('Khóa ngoại đến categories');
            $table->unsignedBigInteger('unit_id')->nullable()->comment('Khóa ngoại đến units_of_measure');
            $table->text('description')->nullable()->comment('Mô tả chi tiết');
            $table->decimal('price', 12, 2)->default(0)->comment('Giá bán');
            $table->decimal('cost', 12, 2)->default(0)->comment('Giá vốn');
            $table->integer('minimum_inventory')->default(10)->comment('Mức tối thiểu đồng');
            $table->integer('total_quantity')->default(50)->comment('Số lượng đồng');
            $table->string('product_image_url', 255)->nullable()->comment('Hình ảnh sản phẩm');
            $table->boolean('is_active')->default(true)->comment('Sản phẩm có hoạt động');
            $table->softDeletes();
            $table->timestamps();
            
            // Foreign keys added in add_foreign_keys_migration
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
            // Index
            $table->index('product_code');
            $table->index('sku');
            $table->index('category_id');
            $table->fullText(['product_name', 'description']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
