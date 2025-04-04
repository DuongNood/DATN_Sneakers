<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    protected $fillable = ['cart_id', 'product_id', 'quantity', 'product_size_id','original_price','discounted_price','total_price'];

    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

    public function product()
    {
        return $this->belongsTo(ProductVariant::class, 'product_id');
    }
    // Mối quan hệ giữa CartItem và ProductSize
    public function productSize()
    {
        return $this->belongsTo(ProductSize::class, 'product_size_id');
    }

    // Mối quan hệ giữa CartItem và Product
}

