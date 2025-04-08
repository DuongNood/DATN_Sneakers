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
        Schema::create('momo_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('order_id')->unique();
            $table->string('partner_code');
            $table->string('request_id');
            $table->decimal('amount', 15, 2); // Số tiền
            $table->string('order_info');
            $table->string('order_type')->nullable();
            $table->string('trans_id')->nullable(); // Mã giao dịch
            $table->integer('result_code')->nullable();
            $table->string('message')->nullable();
            $table->string('pay_type')->nullable();
            $table->string('response_time')->nullable();
            $table->text('extra_data')->nullable();
            $table->string('signature')->nullable();
            $table->string('status')->default('pending'); // Trạng thái đơn hàng: pending, success, failed
            $table->string('payment_method')->default('momo');
            $table->json('response_data')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('momo_transactions');
    }
};
