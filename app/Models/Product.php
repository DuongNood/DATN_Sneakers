<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $fillable =[
        'product_code',
        'product_name',
        'image',
        'description',
        'image',
        'status',
        'is_show_home'
    ];
    protected $casts =[
        'status'=>'boolean', 
        'is_show_home'=>'boolean', 
    ];
    
    // 🔗 Liên kết với `ProductVariant`
    public function variants()
    {
        return $this->hasMany(ProductVariant::class, 'product_id');
    }

    // 🔗 Liên kết với `Category`
    public function category()  
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
}
