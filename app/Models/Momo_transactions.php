<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Momo_transactions extends Model
{
    use HasFactory;

    protected $fillable = [
        "partnerCode",
        "requestId",
        "amount",
        "orderId",
        "orderInfo",
        "redirectUrl",
        "ipnUrl",
        "requestType",
        "extraData",
        "signature",
    ];

    protected $casts = [
        'response_data' => 'array'
    ];
}
