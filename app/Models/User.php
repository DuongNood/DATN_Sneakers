<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens; // Thêm HasApiTokens
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable; // Sử dụng HasApiTokens

    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'password',
        'image_user',
        'role_id',
        'remember_token',
        'email_verified_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'phone' => 'string',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function hasPermission($permissionName)
    {
        return $this->role->permissions->contains('name', $permissionName);
    }

    public function isAdmin()
    {
        return $this->role_id === 1;
    }

    public function isStaff()
    {
        return $this->role_id === 2;
    }
    public function reviews()
    {
        return $this->hasMany(ProductReview::class);
    }
}
    

