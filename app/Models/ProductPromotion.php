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
    
    public function promotion()
    {
        return $this->belongsTo(Promotion::class, 'promotion_id');
    }

    public function productVariant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }
}
