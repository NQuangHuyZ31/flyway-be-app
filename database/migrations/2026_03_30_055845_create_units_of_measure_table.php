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
        Schema::create('units_of_measures', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique()->comment('Đơn vị (Cái, Hộp, Mét, Kg...)');
            $table->string('code', 20)->unique()->comment('Mã đơn vị (PCS, BOX, M, KG...)');
            $table->string('abbreviation', 10)->nullable()->comment('Viết tắt');
            $table->text('description')->nullable()->comment('Mô tả');
            $table->decimal('conversion_factor', 10, 4)->nullable()->comment('Hệ số chuyển đổi');
            $table->boolean('is_active')->default(true)->comment('Đơn vị có hoạt động');
            $table->softDeletes();
            $table->timestamps();
            $table->index('code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('units_of_measure');
    }
};
