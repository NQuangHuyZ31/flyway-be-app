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
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('supplier_name', 255)->comment('Tên nhà cung cấp');
            $table->string('email', 255)->nullable()->unique()->comment('Email');
            $table->string('phone', 20)->comment('Số điện thoại');
            $table->text('address')->comment('Địa chỉ');
            $table->string('city', 100)->comment('Thành phố');
            $table->string('district', 100)->nullable()->comment('Quận/Huyện');
            $table->string('country', 100)->default('Vietnam')->comment('Quốc gia');
            $table->string('tax_code', 50)->nullable()->unique()->comment('Mã số thuế');
            $table->string('contact_person', 255)->nullable()->comment('Nhân vật liên hệ');
            $table->string('contact_email', 100)->nullable()->comment('Email liên hệ');
            $table->string('bank_account', 50)->nullable()->comment('Tài khoản ngân hàng');
            $table->decimal('payment_terms_days', 5, 2)->default(30)->comment('Thời gian thẳng (ngày)');
            $table->decimal('discount_percent', 5, 2)->default(0)->comment('Giậm giá (%)') ;
            $table->decimal('rating', 3, 2)->default(0)->comment('Tiệm chiến (0-5)');
            $table->text('notes')->nullable()->comment('Ghi chú');
            $table->boolean('is_active')->default(true)->comment('Nhà cung cấp có hoạt động');
            $table->softDeletes();
            $table->timestamps();
            
            // Index
            $table->index('email');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};
