<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductPromotion extends Model
{
    use HasFactory;
    protected $fillable =[
        'product_variant_id',
        'Promotion_id',
        
    ];
    
}
