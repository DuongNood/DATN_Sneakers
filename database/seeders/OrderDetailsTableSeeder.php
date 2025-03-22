<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\ProductVariant;
use App\Models\Promotion;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrderDetailsTableSeeder extends Seeder
{
    public function run()
    {
        $orders = Order::pluck('id')->toArray();
        $productVariants = ProductVariant::all();

        if (empty($orders) || $productVariants->isEmpty()) {
            return;
        }

        $orderDetails = [];
        foreach ($orders as $orderId) {
            $selectedProducts = $productVariants->random(rand(1, 3));

            foreach ($selectedProducts as $product) {
                $quantity = rand(1, 5);
                $price = $product->price;

                // Kiểm tra xem có khuyến mãi không
                $promotion = Promotion::where('start_date', '<=', now())
                    ->where('end_date', '>=', now())
                    ->whereHas('productPromotions', function ($query) use ($product) {
                        $query->where('product_variant_id', $product->id);
                    })
                    ->first();

                $discount = 0;
                if ($promotion) {
                    if ($promotion->discount_type === Promotion::SO_TIEN) {
                        $discount = min($promotion->discount_value, $promotion->max_discount_value);
                    } else {
                        $discount = min(($price * $promotion->discount_value) / 100, $promotion->max_discount_value);
                    }
                }

                $totalPrice = ($price - $discount) * $quantity;

                $orderDetails[] = [
                    'order_id'           => $orderId,
                    'product_variant_id' => $product->id,
                    'quantity'           => $quantity,
                    'price'              => $price,
                    'discount'           => $discount,
                    'total_price'        => $totalPrice,
                    'created_at'         => now(),
                    'updated_at'         => now(),
                ];
            }
        }

        DB::table('order_details')->insert($orderDetails);
    }
}
