<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OderDetail extends Model
{
    use HasFactory;
    protected $fillable =[
        'oder_id',
        'product_variant_id',
        'quantity',
        'price',
        'discount',
        'total_price',
    ];
    public function oder(){
        return $this->belongsTo(Oder::class, 'oder_id');
    }
    public function product(){
        return $this->belongsTo(Product::class);
    }
    
    public function productVariant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    public function promotion()
    {
        return $this->hasOneThrough(Promotion::class, ProductPromotion::class, 'product_variant_id', 'id', 'product_variant_id', 'promotion_id')
            ->where('promotions.start_date', '<=', now())
            ->where('promotions.end_date', '>=', now());
    }

    public function getFinalPriceAttribute()
    {
        $discount = $this->discount ?? 0;
        return $this->price - $discount;
    }
}
