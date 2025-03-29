<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    protected $fillable = ['cart_id', 'product_id', 'quantity'];

    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

    public function product()
    {
<<<<<<< HEAD
        return $this->belongsTo(ProductVariant::class, 'products_id');
    }}
=======
        return $this->belongsTo(Product::class);
    }
}
>>>>>>> parent of f0d2918 (Fix merge conflict in ProductController.php and api.php)
