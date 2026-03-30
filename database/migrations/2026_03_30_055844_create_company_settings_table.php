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
        Schema::create('company_settings', function (Blueprint $table) {
            $table->id();
            $table->string('company_name', 255)->comment('Tên công ty');
            $table->string('company_code', 50)->unique()->comment('Mã công ty');
            $table->text('address')->comment('Địa chỉ');
            $table->string('city', 100)->comment('Thành phố');
            $table->string('district', 100)->comment('Quận/Huyện');
            $table->string('country', 100)->default('Vietnam')->comment('Quốc gia');
            $table->string('phone', 20)->nullable()->comment('Số điện thoại');
            $table->string('email', 100)->nullable()->comment('Email');
            $table->string('tax_code', 50)->nullable()->unique()->comment('Mã số thuế');
            $table->string('logo_url', 255)->nullable()->comment('URL logo công ty');
            $table->string('website', 255)->nullable()->comment('Website');
            $table->string('currency', 3)->default('VND')->comment('Ngoại tệ mặc định');
            $table->string('timezone', 50)->default('Asia/Ho_Chi_Minh')->comment('Múi giờ');
            $table->text('company_info')->nullable()->comment('Thông tin chi tiết công ty');
            $table->boolean('is_active')->default(true)->comment('Công ty có hoạt động không');
            $table->timestamps();
            $table->index('company_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_settings');
    }
};
