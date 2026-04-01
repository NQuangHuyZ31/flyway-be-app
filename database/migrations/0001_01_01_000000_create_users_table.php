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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('birthday')->nullable()->comment('Ngày sinh');
            $table->enum('department', ['HR', 'Engineering', 'Sales', 'Marketing', 'Finance', 'Inventory'])->nullable()->comment('Phòng ban');
            $table->text('avatar')->nullable()->comment('ảnh đại diện');
            $table->string('phone')->nullable()->comment('Số điện thoại');
            $table->string('province')->nullable()->comment('Tỉnh/Thành phố');
            $table->string('ward')->nullable()->comment('Phường/Xã');
            $table->string('address')->nullable()->comment('Địa chỉ');
            $table->boolean('status')->default(0)->comment('Trạng thái tài khoản');
            $table->string('password');
            $table->rememberToken();    
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
