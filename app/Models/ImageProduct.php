<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImageProduct extends Model
{
    use HasFactory;
    protected $fillable =[
        'product_id',
        'image_product',     
    ];
    protected $casts =[
        'status'=>'boolean' 
    ];
    public function Product(){
        return $this->belongsTo(Product::class);
    }
}
