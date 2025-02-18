<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OderDetail extends Model
{
    use HasFactory;
    protected $fillable =[
        'product_variant_id',
        'price',
        'quantity',
        'total_price'
    ];
    public function oder(){
        return $this->belongsTo(Oder::class);
    }
    public function product(){
        return $this->belongsTo(Product::class);
    }
    
}
