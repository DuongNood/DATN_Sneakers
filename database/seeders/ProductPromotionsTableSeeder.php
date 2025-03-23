<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductPromotionsTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('product_promotions')->insert([
            ['product_variant_id' => 1, 'promotion_id' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['product_variant_id' => 2, 'promotion_id' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['product_variant_id' => 3, 'promotion_id' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['product_variant_id' => 4, 'promotion_id' => 4, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
