<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Momo_transactions extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'amount',
        'trans_id',
        'status',
        'response_data'
    ];

    protected $casts = [
        'response_data' => 'array'
    ];
}
