<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductSize extends Model
{
    use HasFactory;

    protected $fillable =[
        'name',
    ];
    protected $table = 'product_sizes'; // Đặt đúng tên bảng nếu cần
}

