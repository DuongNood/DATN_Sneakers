<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductReview extends Model
{
    use HasFactory;
     protected $fillable = [
        'user_id',
        'product_variant_id',
        'order_detail_id',
        'rating',
        'comment',
    ];

    // Quan hệ: mỗi review thuộc về 1 người dùng
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Quan hệ: mỗi review thuộc về 1 sản phẩm
    public function productVariant()
    {
        return $this->belongsTo(ProductVariant::class);
    }

    // Quan hệ: mỗi review liên kết với 1 đơn hàng cụ thể
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
