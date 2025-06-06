<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    use HasFactory;

    protected $fillable =[
        'title',
        'image',
        'status',
    ];

    public $attributes = [
        'status' => 0,
    ];

    // protected $fillable = ['title', 'image', ];

}
