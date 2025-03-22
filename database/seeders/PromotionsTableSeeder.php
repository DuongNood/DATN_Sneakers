<?php

namespace Database\Seeders;

use App\Models\Promotion;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PromotionsTableSeeder extends Seeder
{
    public function run()
    {
        $promotions = [
            [
                'promotion_name'     => 'Giảm 10% toàn bộ đơn hàng',
                'discount_type'      => Promotion::PHAN_TRAM,
                'discount_value'     => 10, // Giảm 10%
                'start_date'         => now()->subDays(5),
                'end_date'           => now()->addDays(10),
                'max_discount_value' => 200000, // Giảm tối đa 200K
                'description'        => 'Khuyến mãi giảm giá 10% cho tất cả đơn hàng trên hệ thống.',
                'status'             => true,
                'created_at'         => now(),
                'updated_at'         => now(),
            ],
            [
                'promotion_name'     => 'Giảm ngay 150K cho đơn từ 1.500.000',
                'discount_type'      => Promotion::SO_TIEN,
                'discount_value'     => 150000, // Giảm 150K
                'start_date'         => now()->subDays(2),
                'end_date'           => now()->addDays(5),
                'max_discount_value' => 150000, // Không vượt quá 150K
                'description'        => 'Áp dụng cho đơn hàng từ 1.500.000 VND trở lên.',
                'status'             => true,
                'created_at'         => now(),
                'updated_at'         => now(),
            ],
            [
                'promotion_name'     => 'Giảm 20% tối đa 300K',
                'discount_type'      => Promotion::PHAN_TRAM,
                'discount_value'     => 20, // Giảm 20%
                'start_date'         => now()->subDays(10),
                'end_date'           => now()->addDays(15),
                'max_discount_value' => 300000, // Tối đa giảm 300K
                'description'        => 'Giảm giá 20% trên tổng hóa đơn, tối đa giảm 300K.',
                'status'             => true,
                'created_at'         => now(),
                'updated_at'         => now(),
            ],
            [
                'promotion_name'     => 'Flash Sale - Giảm 50K',
                'discount_type'      => Promotion::SO_TIEN,
                'discount_value'     => 50000, // Giảm 50K
                'start_date'         => now(),
                'end_date'           => now()->addDays(3),
                'max_discount_value' => 50000,
                'description'        => 'Chỉ áp dụng cho đơn hàng trong 3 ngày sale đặc biệt.',
                'status'             => true,
                'created_at'         => now(),
                'updated_at'         => now(),
            ],
            [
                'promotion_name'     => 'Khuyến mãi cuối tuần - Giảm 15%',
                'discount_type'      => Promotion::PHAN_TRAM,
                'discount_value'     => 15, // Giảm 15%
                'start_date'         => now()->next('Saturday'),
                'end_date'           => now()->next('Sunday'),
                'max_discount_value' => 250000, // Giảm tối đa 250K
                'description'        => 'Chương trình áp dụng vào cuối tuần.',
                'status'             => true,
                'created_at'         => now(),
                'updated_at'         => now(),
            ],
            [
                'promotion_name'     => 'Mua nhiều giảm nhiều - Giảm 5%',
                'discount_type'      => Promotion::PHAN_TRAM,
                'discount_value'     => 5, // Giảm 5%
                'start_date'         => now()->subDays(3),
                'end_date'           => now()->addDays(20),
                'max_discount_value' => 100000, // Giảm tối đa 100K
                'description'        => 'Áp dụng khi mua từ 2 sản phẩm trở lên.',
                'status'             => true,
                'created_at'         => now(),
                'updated_at'         => now(),
            ],
            [
                'promotion_name'     => 'Giảm ngay 300K cho đơn từ 2.500.000',
                'discount_type'      => Promotion::SO_TIEN,
                'discount_value'     => 300000, // Giảm 300K
                'start_date'         => now()->subDays(1),
                'end_date'           => now()->addDays(7),
                'max_discount_value' => 300000, // Không vượt quá 300K
                'description'        => 'Chỉ áp dụng cho đơn hàng từ 2.500.000 VND trở lên.',
                'status'             => true,
                'created_at'         => now(),
                'updated_at'         => now(),
            ],
        ];

        // Chèn dữ liệu vào bảng `promotions`
        DB::table('promotions')->insert($promotions);
    }
}
