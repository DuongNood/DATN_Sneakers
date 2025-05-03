<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVnpayTransactionsTable extends Migration
{
    public function up()
    {
        Schema::create('vnpay_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id')->unique();
            $table->string('vnp_transaction_no')->nullable();
            $table->decimal('vnp_amount', 15, 2);
            $table->string('vnp_bank_code')->nullable();
            $table->string('vnp_bank_tran_no')->nullable();
            $table->string('vnp_card_type')->nullable();
            $table->dateTime('vnp_pay_date')->nullable();
            $table->string('vnp_response_code')->nullable();
            $table->string('vnp_transaction_status')->nullable();
            $table->string('vnp_secure_hash')->nullable();
            $table->timestamps();

            $table->foreign('order_id')
                  ->references('id')
                  ->on('orders')
                  ->onDelete('cascade');
        });

        // Cập nhật cột payment_method trong bảng orders
        Schema::table('orders', function (Blueprint $table) {
            $table->enum('payment_method', ['cod', 'momo', 'vnpay'])
                  ->default('cod')
                  ->change();
        });
    }

    public function down()
    {
        Schema::dropIfExists('vnpay_transactions');

        Schema::table('orders', function (Blueprint $table) {
            $table->enum('payment_method', ['cod', 'momo'])
                  ->default('cod')
                  ->change();
        });
    }
}