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
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->string('movement_code', 50)->unique();
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('variant_id')->nullable();
            $table->unsignedBigInteger('from_warehouse_id');
            $table->unsignedBigInteger('from_section_id')->nullable();
            $table->unsignedBigInteger('to_warehouse_id');
            $table->unsignedBigInteger('to_section_id')->nullable();
            $table->integer('quantity');
            $table->date('movement_date');
            $table->unsignedBigInteger('user_id');
            $table->enum('status', ['pending', 'shipped', 'received', 'cancelled'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();
            // Foreign keys added in add_foreign_keys_migration
            $table->index('movement_code');
            $table->index('from_warehouse_id');
            $table->index('to_warehouse_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
