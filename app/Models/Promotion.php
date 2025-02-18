<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{
    use HasFactory;
    protected $fillable =[
        'promotion_name',
        'discount_type',
        'discount_value',
        'start_date',
        'end_date',
        'max_discount_value',
        'description',
        'status',
        
    ];
    protected $casts =[
        'status'=>'boolean' 
    ];
    const PHAN_TRAM= 'Giảm theo %';
    const SO_TIEN= 'Giảm số tiền';
}
