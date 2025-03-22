<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductVariantsTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('product_variants')->insert([
            ['product_id' => 1, 'size' => '40', 'price' => 1200000, 'promotional_price' => 1000000, 'quantity' => 10, 'created_at' => now(), 'updated_at' => now()],
            ['product_id' => 1, 'size' => '42', 'price' => 1200000, 'promotional_price' => 1000000, 'quantity' => 8, 'created_at' => now(), 'updated_at' => now()],
            ['product_id' => 2, 'size' => '41', 'price' => 2000000, 'promotional_price' => 1800000, 'quantity' => 5, 'created_at' => now(), 'updated_at' => now()],
            ['product_id' => 3, 'size' => '43', 'price' => 900000, 'promotional_price' => null, 'quantity' => 12, 'created_at' => now(), 'updated_at' => now()],
            ['product_id' => 4, 'size' => '39', 'price' => 850000, 'promotional_price' => 800000, 'quantity' => 6, 'created_at' => now(), 'updated_at' => now()],
            ['product_id' => 5, 'size' => '42', 'price' => 3000000, 'promotional_price' => 2900000, 'quantity' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['product_id' => 6, 'size' => '40', 'price' => 2700000, 'promotional_price' => 2500000, 'quantity' => 7, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
