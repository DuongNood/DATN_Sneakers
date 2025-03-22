<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class ProductsTableSeeder extends Seeder
{
    public function run()
    {
        $products = [
            ['Nike Air Force 1', 'Nike', 1200000],
            ['Adidas Ultraboost', 'Adidas', 1500000],
            ['Puma Running Shoes', 'Puma', 1000000],
            ['Vans Old Skool', 'Vans', 850000],
            ['Converse Chuck Taylor', 'Converse', 900000],
            ['Adidas NMD R1', 'Adidas', 1700000],
            ['Adidas Superstar', 'Adidas', 1300000],
            ['Reebok Classic', 'Reebok', 1300000],
            ['Nike Air Max 270', 'Nike', 1900000],
            ['Nike React Infinity', 'Nike', 2200000],
            ['Asics Gel-Kayano', 'Asics', 1400000],
            ['New Balance 574', 'New Balance', 1600000],
            ['Under Armour HOVR', 'Under Armour', 1800000],
            ['Fila Disruptor', 'Fila', 950000],
            ['Balenciaga Triple S', 'Balenciaga', 3000000],
            ['Yeezy Boost 350', 'Yeezy', 2800000],
            ['Nike Air Max 90', 'Nike', 1350000],
            ['Adidas Stan Smith', 'Adidas', 1000000],
            ['Adidas Forum Low', 'Adidas', 1450000],
            ['Adidas 4DFWD', 'Adidas', 2750000],
            ['Puma Suede Classic', 'Puma', 1100000],
            ['Timberland Boots', 'Timberland', 2500000],
            ['Puma RS-X', 'Puma', 1600000],
            ['Puma Future Rider', 'Puma', 1400000],
            ['Dr. Martens 1460', 'Dr. Martens', 2200000],
            ['Gucci Ace Sneakers', 'Gucci', 3000000],
            ['Nike ZoomX Vaporfly', 'Nike', 2900000],
            ['Nike Pegasus Trail 4', 'Nike', 1800000],
            ['Jordan 1 Retro', 'Jordan', 2700000],
            ['Salomon Speedcross', 'Salomon', 1900000],
            ['Puma Deviate Nitro', 'Puma', 2000000],
        ];

        // Danh sách ảnh cố định cho từng sản phẩm
        $imagePaths = [
            'admins/images/products/shoes.jpg',
            'admins/images/products/bags.jpg',
            'admins/images/products/dresses.jpg',
            'admins/images/products/headphone.jpg',
        ];

        foreach ($products as $index => $product) {
            $categoryId = rand(1, 5); // Giả lập danh mục

            // Tạo product_code ngẫu nhiên
            $productCode = strtoupper(substr($product[1], 0, 3)) . '-' . rand(100, 999);

            // Giá khuyến mãi
            $discountPercentage = rand(5, 20);
            $discountedPrice = (rand(0, 1)) ? $product[2] * (1 - $discountPercentage / 100) : $product[2];

            // Chèn sản phẩm vào bảng `products`
            $productId = DB::table('products')->insertGetId([
                'product_code'    => $productCode,
                'product_name'    => $product[0],
                'category_id'     => $categoryId,
                'original_price'  => $product[2],
                'discounted_price' => $discountedPrice,
                'status'          => rand(0, 1),
                'is_show_home'    => rand(0, 1),
                'description'     => Str::random(50),
                'created_at'      => now(),
                'updated_at'      => now(),
            ]);

            // Chèn 2-3 ảnh cho mỗi sản phẩm vào bảng `image_products`
            $numImages = rand(2, 3);
            for ($i = 0; $i < $numImages; $i++) {
                DB::table('image_products')->insert([
                    'product_id'   => $productId,
                    'image_product' => $imagePaths[array_rand($imagePaths)],
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ]);
            }
        }
    }
}
