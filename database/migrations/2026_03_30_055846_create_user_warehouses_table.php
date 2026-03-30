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
        Schema::create('user_warehouses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->comment('Khóa ngoại đến users');
            $table->unsignedBigInteger('warehouse_id')->comment('Khóa ngoại đến warehouses');
            $table->enum('assignment_type', ['primary', 'secondary'])->default('primary')->comment('Vai trò phân công');
            $table->timestamp('assigned_date')->useCurrent()->comment('Ngày phân công');
            $table->timestamp('assigned_end_date')->nullable()->comment('Ngày kết thúc phân công');
            $table->boolean('is_active')->default(true)->comment('Phân công có hoạt động');
            $table->timestamps();
            
            // Foreign keys added in add_foreign_keys_migration
            
            // Index
            $table->unique(['user_id', 'warehouse_id']);
            $table->index('user_id');
            $table->index('warehouse_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_warehouses');
    }
};
