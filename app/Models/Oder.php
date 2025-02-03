<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Oder extends Model
{
    use HasFactory;
    protected $fillable =[
        'user_id',
        'recipient_name',
        'recipient_phone',
        'recipient_address',
        'total_price',
        'shipping_fee',
        'payment_method',
        'payment_status',
        'status',
        
    ];
    protected $casts =[
        'status'=>'boolean' 
    ];
   
    const TRANG_THAI_THANH_TOAN =[
        'chua_thanh_toan'=>'chưa thanh toán',
        'da_thanh_toan'=>'đã thanh toán',
    ];
    const TRANG_THAI_DON_HANG = [
        'cho_xac_nhan'=>'chờ xác nhận',
        'da_xac_nhan'=>'đã xác nhận',
        'dang_chuan_bi'=>'đang chuẩn bị',
        'dang_van_chuyen'=>'đang vận chuyển',
        'da_giang_hang'=>'đã giao hàng',
        'huy_don_hang'=>'hủy đơn hàng'
    ];
    const CHO_XAC_NHAN  = 'cho_xac_nhan';
    const DA_XAC_NHAN = 'da_xac_nhan';
    const DANG_CHUAN_BI = 'dang_chuan_bi';
    const DANG_VAN_CHUYEN = 'dang_van_chuyen';
    const DA_GIANG_HANG = 'da_giang_hang';
    const HUY_DON_HANG = 'huy_don_hang';
    const CHUA_THANH_TOAN ='chua_thanh_toan';
    const DA_THANH_TOAN ='da_thanh_toan';
     public function user(){
       return $this->belongsTo(User::class);
    }
    public function oderDetail(){
       return $this->hasMany(OderDetail::class);
    }
}
