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
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255)->comment('Tên danh mục');
            $table->string('slug', 255)->comment('URL slug');
            $table->text('description')->nullable()->comment('Mô tả danh mục');
            $table->string('icon', 100)->nullable()->comment('Icon danh mục');
            $table->string('thumbnail_url', 255)->nullable()->comment('Hình ảnh thumbnail');
            $table->integer('display_order')->default(0)->comment('Thứ tự hiẳn thị');
            $table->boolean('is_active')->default(true)->comment('Danh mục có hoạt động');
            $table->softDeletes();
            $table->timestamps();
            
            // Foreign keys added in add_all_foreign_keys migration
            
            // Index
            $table->index('slug');
            $table->index('display_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
