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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('customer_name', 255)->comment('Tên khách hàng');
            $table->unsignedInteger('supplier_id')->nullable()->comment('Khóa ngoại liên quan đến nhà cung cấp');
            $table->string('email', 255)->nullable()->unique()->comment('Email');
            $table->string('phone', 20)->comment('Số điện thoại');
            $table->text('address')->comment('Địa chỉ');
            $table->string('city', 100)->comment('Thành phố');
            $table->string('district', 100)->nullable()->comment('Quận/Huyện');
            $table->string('country', 100)->default('Vietnam')->comment('Quốc gia');
            $table->string('tax_code', 50)->nullable()->unique()->comment('Mã số thuế');
            $table->string('contact_person', 255)->nullable()->comment('Nhân vật liên hệ');
            $table->string('contact_phone', 20)->nullable()->comment('Số điện thoại liên hệ');
            $table->enum('customer_type', ['retail', 'customer', 'distributor', 'corporate'])->default('customer')->comment('Loại khách hàng');
            $table->decimal('credit_limit', 15, 2)->default(0)->comment('Hạn mủc tín dụng');
            $table->decimal('current_debt', 15, 2)->default(0)->comment('Nợ hiện tại');
            $table->text('notes')->nullable()->comment('Ghi chú');
            $table->boolean('is_active')->default(true)->comment('Khách hàng có hoạt động');
            $table->softDeletes();
            $table->timestamps();
            
            // Index
            $table->index('email');
            $table->index('customer_type');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
