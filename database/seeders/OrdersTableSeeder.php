<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrdersTableSeeder extends Seeder
{
    public function run()
    {
        $users = User::pluck('id')->toArray();

        $orders = [];
        for ($i = 0; $i < 10; $i++) {
            $orderCode = 'ODR' . time() . rand(1000, 9999);
            $totalPrice = rand(800000, 3000000);

            $orders[] = [
                'user_id'           => $users[array_rand($users)], // Chọn ngẫu nhiên 1 user
                'order_code'        => $orderCode,
                'recipient_name'    => 'Nguyễn Văn ' . Str::random(3),
                'recipient_phone'   => '+84' . rand(100000000, 999999999),
                'recipient_address' => 'Số ' . rand(1, 99) . ', Quận ' . rand(1, 12) . ', TP.HCM',
                'total_price'       => $totalPrice,
                'shipping_fee'      => 15000,
                'payment_method'    => rand(0, 1) ? 'COD' : 'Online',
                'payment_status'    => rand(0, 1) ? Order::DA_THANH_TOAN : Order::CHUA_THANH_TOAN,
                'status'            => array_rand(Order::ORDER_STATUS), // Lấy ngẫu nhiên trạng thái đơn hàng
                'created_at'        => now(),
                'updated_at'        => now(),
            ];
            sleep(1); // Tránh trùng time() khi tạo order_code
        }

        DB::table('orders')->insert($orders);
    }
}
