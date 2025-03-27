<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $table = 'orders';

    protected $fillable = [
        'user_id', 'order_code', 'recipient_name', 'recipient_phone',
        'recipient_address', 'total_price', 'promotion', 'shipping_fee',
        'payment_method', 'payment_status', 'status'
    ];

    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class, 'order_id');
    }

    protected $casts = [
        'total_price' => 'decimal:2',
        'shipping_fee' => 'decimal:2',
    ];

    // Trạng thái thanh toán
    const PAYMENT_STATUS = [
        'chua_thanh_toan' => 'Chưa thanh toán',
        'da_thanh_toan' => 'Đã thanh toán',
    ];

    // Trạng thái đơn hàng
    const ORDER_STATUS = [
        'cho_xac_nhan' => 'Chờ xác nhận',
        'dang_chuan_bi' => 'Đang chuẩn bị',
        'dang_van_chuyen' => 'Đang vận chuyển',
        'da_giao_hang' => 'Đã giao hàng',
        'huy_don_hang' => 'Hủy đơn hàng',
    ];

    // Định nghĩa trạng thái
    const CHO_XAC_NHAN = 'cho_xac_nhan';
    const DANG_CHUAN_BI = 'dang_chuan_bi';
    const DANG_VAN_CHUYEN = 'dang_van_chuyen';
    const DA_GIAO_HANG = 'da_giao_hang';
    const HUY_DON_HANG = 'huy_don_hang';
    const CHUA_THANH_TOAN = 'chua_thanh_toan';
    const DA_THANH_TOAN = 'da_thanh_toan';

    /**
     * Quan hệ với bảng users
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Quan hệ với bảng order_details
     */

    /**
     * Scope lọc đơn hàng theo trạng thái
     */
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Mutator: Chuẩn hóa số điện thoại
     */
    public function setRecipientPhoneAttribute($value)
    {
        $this->attributes['recipient_phone'] = preg_match('/^\+84/', $value) ? $value : '+84' . ltrim($value, '0');
    }

    /**
     * Mutator: Định dạng tên người nhận (Viết hoa chữ cái đầu)
     */
    public function setRecipientNameAttribute($value)
    {
        $this->attributes['recipient_name'] = ucwords(strtolower($value));
    }

    /**
     * Lấy trạng thái đơn hàng theo format dễ đọc hơn
     */
    public function getStatusTextAttribute()
    {
        return self::ORDER_STATUS[$this->status] ?? 'Không xác định';
    }
    public function productSize()
{
    return $this->belongsTo(ProductSize::class, 'product_size_id');
}

}

