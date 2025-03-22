<?php

use App\Models\Category;
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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('product_code')->unique();
            $table->string('product_name')->unique();
            $table->string('image')->nullable();
            $table->text('description')->nullable();
            $table->double('original_price')->after('some_column'); // Giá gốc 
            $table->double('discounted_price')->nullable()->after('original_price'); // Giá sau giảm giá mặc định
            $table->foreignIdFor(Category::class)->constrained();
            $table->boolean('status')->default(true);
            $table->boolean('is_show_home')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
