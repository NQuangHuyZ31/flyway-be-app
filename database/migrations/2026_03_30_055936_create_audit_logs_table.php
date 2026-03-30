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
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('model_type', 255);
            $table->unsignedBigInteger('model_id')->nullable();
            $table->enum('action', ['create', 'update', 'delete', 'view', 'export', 'import'])->comment('Hành động');
            $table->json('old_values')->nullable()->comment('Giá trị cũ');
            $table->json('new_values')->nullable()->comment('Giá trị mới');
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
            
            // Foreign keys added in add_all_foreign_keys migration
            
            // Index
            $table->index('user_id');
            $table->index('model_type');
            $table->index('action');
            $table->index('created_at');
            $table->index(['model_type', 'model_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
