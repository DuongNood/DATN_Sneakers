<?php

use App\Models\Oder;
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
        Schema::create('oders', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class)->constrained();
            $table->string('order_code')->unique();
            $table->string('recipient_name');
            $table->string('recipient_phone');
            $table->string('recipient_address');
            $table->double('total_price');
            $table->double('shipping_fee');
            $table->enum('payment_method',['COD', 'Online'])->nullable();
            $table->string('payment_status')->default(Oder::CHUA_THANH_TOAN);
            $table->string('status')->default(Oder::CHO_XAC_NHAN);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('oders');
    }
};
