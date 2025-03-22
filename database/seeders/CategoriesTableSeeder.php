<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoriesTableSeeder extends Seeder
{
    public function run()
    {
        $categories = [
            ['category_name' => 'Nike', 'created_at' => now(), 'updated_at' => now(), 'created_at' => now(), 'updated_at' => now()],
            ['category_name' => 'Adidas', 'created_at' => now(), 'updated_at' => now()],
            ['category_name' => 'Puma', 'created_at' => now(), 'updated_at' => now()],
            ['category_name' => 'Vans', 'created_at' => now(), 'updated_at' => now()],
            ['category_name' => 'Converse', 'created_at' => now(), 'updated_at' => now()],
            ['category_name' => 'Reebok', 'created_at' => now(), 'updated_at' => now()],
            ['category_name' => 'Asics', 'created_at' => now(), 'updated_at' => now()],
            ['category_name' => 'New Balance', 'created_at' => now(), 'updated_at' => now()],
            ['category_name' => 'Under Armour', 'created_at' => now(), 'updated_at' => now()],
            ['category_name' => 'Fila', 'created_at' => now(), 'updated_at' => now()],
            ['category_name' => 'Balenciaga', 'created_at' => now(), 'updated_at' => now()],
            ['category_name' => 'Yeezy', 'created_at' => now(), 'updated_at' => now()],
            ['category_name' => 'Timberland', 'created_at' => now(), 'updated_at' => now()],
            ['category_name' => 'Dr. Martens', 'created_at' => now(), 'updated_at' => now()],
            ['category_name' => 'Gucci', 'created_at' => now(), 'updated_at' => now()],
            ['category_name' => 'Jordan', 'created_at' => now(), 'updated_at' => now()],
            ['category_name' => 'Salomon', 'created_at' => now(), 'updated_at' => now()],
        ];

        DB::table('categories')->insert($categories);
    }
}
