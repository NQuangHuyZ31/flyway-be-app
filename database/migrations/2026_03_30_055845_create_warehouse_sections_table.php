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
        Schema::create('warehouse_sections', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('warehouse_id')->comment('Khóa ngoại đến warehouses');
            $table->string('section_name', 255)->comment('Tên khu vực');
            $table->string('section_code', 50)->comment('Mã khu vực (VĐ: A1, B2, C3)');
            $table->enum('section_type', ['rack', 'shelf', 'cage', 'bin', 'zone'])->default('zone')->comment('Loại khu vực');
            $table->integer('shelves_count')->nullable()->comment('Số giá');
            $table->integer('racks_count')->nullable()->comment('Số kệ');
            $table->text('notes')->nullable()->comment('Ghi chú');
            $table->boolean('is_active')->default(true)->comment('Khu vực có hoạt động');
            $table->softDeletes();
            $table->timestamps();
            
            // Khóa ngoại
            $table->foreign('warehouse_id')->references('id')->on('warehouses')->onDelete('cascade');
            
            // Index
            $table->unique(['warehouse_id', 'section_code']);
            $table->index('warehouse_id');
            $table->index('section_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warehouse_sections');
    }
};
