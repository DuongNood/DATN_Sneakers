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
        //
        Schema::table('product_variants', function (Blueprint $table) {
            // Xóa unique cũ
            $table->dropUnique('product_variants_sku_unique');
            // Tạo unique mới cho cặp product_id + sku
            $table->unique(['product_id', 'sku']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::table('product_variants', function (Blueprint $table) {
            $table->dropUnique(['product_id', 'sku']);
            $table->unique('sku'); // Khôi phục unique cũ nếu rollback
        });
    }
};
