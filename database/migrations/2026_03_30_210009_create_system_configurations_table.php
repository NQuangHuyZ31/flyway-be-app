<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Cấu hình hệ thống (System Configurations)
     * = Lưu trữ các setting toàn hệ thống
     * = Vd: tax rate, default warehouse, rounding rules, etc
     */
    public function up(): void
    {
        Schema::create('system_configurations', function (Blueprint $table) {
            $table->id()->comment('ID cấu hình');
            $table->string('config_key', 100)->unique()->comment('Khóa cấu hình (VD: tax_rate, default_warehouse_id)');
            $table->string('label', 255)->comment('Nhãn hiển thị');
            $table->text('value')->nullable()->comment('Giá trị cấu hình');
            $table->enum('config_type', ['string', 'integer', 'decimal', 'boolean', 'date', 'json'])->default('string')->comment('Loại dữ liệu');
            $table->text('description')->nullable()->comment('Mô tả cấu hình');
            
            // Grouping & Organization
            $table->string('config_group', 50)->index()->comment('Nhóm cấu hình (VD: financial, inventory, system)');
            $table->integer('display_order')->default(0)->comment('Thứ tự hiển thị');
            
            // Validation & Constraints
            $table->text('validation_rules')->nullable()->comment('Quy tắc kiểm chứng (JSON)');
            $table->text('allowed_values')->nullable()->comment('Giá trị cho phép (JSON, dùng cho select)');
            $table->text('default_value')->nullable()->comment('Giá trị mặc định');
            
            // Environment specific
            $table->string('environment', 50)->nullable()->comment('Môi trường áp dụng (development, production, staging - NULL = tất cả)');
            
            // Version & History
            $table->integer('version')->default(1)->comment('Phiên bản');
            $table->unsignedBigInteger('last_modified_by')->nullable()->comment('Khóa ngoại: Người sửa cuối cùng');
            
            // Lock for critical configs
            $table->boolean('is_locked')->default(false)->comment('Khóa cấu hình (prevent modification)');
            $table->boolean('is_encrypted')->default(false)->comment('Mã hóa giá trị (cho sensitive data)');
            $table->boolean('is_active')->default(true)->comment('Cấu hình có hoạt động');
            
            $table->timestamps();

            // Khóa ngoại
            $table->foreign('last_modified_by')
                ->references('id')->on('users')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_configurations');
    }
};
