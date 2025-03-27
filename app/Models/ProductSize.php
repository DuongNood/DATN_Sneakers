<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductSize extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function productVariants()
    {
        return $this->hasMany(ProductVariant::class, 'product_size_id');
    }
    protected $table = 'product_sizes'; // Đặt đúng tên bảng nếu cần
}

