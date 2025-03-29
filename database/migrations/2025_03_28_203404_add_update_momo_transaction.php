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
        Schema::table('momo_transactions', function (Blueprint $table) {
          $table->dropColumn('order_id');
          $table->dropColumn('trans_id');
          $table->dropColumn('status');
          $table->dropColumn('response_data');
          $table->string('partnerCode');
          $table->string('requestId');
          $table->string('orderId');
          $table->string('orderInfo');
          $table->string('redirectUrl');
          $table->string('ipnUrl');
          $table->string('requestType');
          $table->string('signature');
          $table->string('extraData')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('momo_transactions', function (Blueprint $table) {
            //
        });
    }
};
