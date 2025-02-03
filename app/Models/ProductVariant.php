<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    use HasFactory;
    protected $fillable =[
        'sku',
        'product_id',
        'price',
        'promotional_price',
        'quantity',
        'status'
    ];
    protected $casts =[
        'status'=>'boolean' 
    ];
}
