<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    use HasFactory;
    protected $fillable =[
        'brand_name',       
        'description',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',      
    ];
    public function productBrand(){
        return $this->hasMany(Product::class, 'brand_id');
    }
    public $timestamps = false;

}
