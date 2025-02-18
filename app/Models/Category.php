<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    protected $fillable =[
        'category_name',
        'image',
        'status'
    ];
    protected $casts =[
        'status'=>'boolean' 
    ];
    public function Product(){
        return $this->hasMany(Product::class);
    }
}
