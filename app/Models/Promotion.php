<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{
    use HasFactory;
    protected $fillable =[
        'promotion_name',
        'discount_type',
        'discount_value',
        'start_date',
        'end_date',
        'max_discount_value',
        'description',
        'status',
        
    ];
    
    const PHAN_TRAM= 'Giảm theo %';
    const SO_TIEN= 'Giảm số tiền';

    public function productPromotions()
    {
        return $this->hasMany(ProductPromotion::class, 'promotion_id');
    }

    public function productVariants()
    {
        return $this->belongsToMany(ProductVariant::class, 'product_promotions');
    }

    public function isValid()
    {
        return $this->status && now()->between($this->start_date, $this->end_date);
    }

    public function calculateDiscount($originalPrice)
    {
        if ($this->discount_type === self::SO_TIEN) {
            return min($this->discount_value, $this->max_discount_value);
        } elseif ($this->discount_type === self::PHAN_TRAM) {
            return min(($originalPrice * $this->discount_value) / 100, $this->max_discount_value);
        }
        return 0;
    }
}
