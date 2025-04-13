<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gender extends Model
{
    use HasFactory;
    protected $fillable =[
        'gender_name',       
        'status'
    ];

    public function productGender(){
        return $this->hasMany(Product::class, 'gender_id');
    }
}
