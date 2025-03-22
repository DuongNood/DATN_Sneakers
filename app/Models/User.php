<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens; // ✅ Thêm HasApiTokens

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, SoftDeletes; // ✅ Sử dụng HasApiTokens

    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'password',
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
        return $this->hasMany(Oder::class);
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function isAdmin()
    {
        return $this->role_id === 1;
    }

    public function isStaff()
    {
        return $this->role_id === 2;
    }
}
    

