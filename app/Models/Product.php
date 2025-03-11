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

        'is_show_home','name', 'price', 'stock',

        'is_show_home',
        'category_id'

    ];
    protected $casts =[
        'status'=>'boolean', 
        'is_show_home'=>'boolean', 
    ];

    
    // 🔗 Liên kết với `ProductVariant`

    //  public function category()
    // {
    //     return $this->belongsTo(Category::class);
    // }
    public function imageProduct(){
        return $this->hasMany(Imageproduct::class);
    
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class, 'product_id');
    }

    // 🔗 Liên kết với `Category`

    public function category()  
    {
        return $this->belongsTo(Category::class, 'category_id');

    }
    // protected $fillable = ['name', 'price', 'stock'];

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);

    }

    
    

}
