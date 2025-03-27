<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    use HasFactory;
    
    protected $fillable = [

        'order_id',
        'product_id', //  Đảm bảo có dòng này
        'quantity',
        'price',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function productVariant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_size_id');
    }
    public function order(){
        return $this->belongsTo(Order::class, 'order_id');
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
