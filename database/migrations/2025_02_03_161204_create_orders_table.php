<?php

use App\Models\Order;
use App\Models\User;
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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class)->constrained();
            $table->string('order_code')->unique();
            $table->string('recipient_name');
            $table->string('recipient_phone');
            $table->string('recipient_address');
            $table->double('promotion')->default(0);
            $table->double('shipping_fee')->default(25000);
            $table->double('total_price');
            $table->enum('payment_method',['COD', 'Online'])->nullable();
            $table->string('payment_status')->default(Order::CHUA_THANH_TOAN);
            $table->string('status')->default(Order::CHO_XAC_NHAN);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
