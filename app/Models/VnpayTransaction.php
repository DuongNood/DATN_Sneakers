<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VnpayTransaction extends Model
{
    use HasFactory;

    protected $table = 'vnpay_transactions';

    protected $fillable = [
        'order_id',
        'vnp_transaction_no',
        'vnp_amount',
        'vnp_bank_code',
        'vnp_bank_tran_no',
        'vnp_card_type',
        'vnp_pay_date',
        'vnp_response_code',
        'vnp_transaction_status',
        'vnp_secure_hash',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}