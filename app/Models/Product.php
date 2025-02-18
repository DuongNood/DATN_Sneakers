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
     public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function imageProduct(){
        return $this->hasMany(Imageproduct::class);
    }
}
