<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run()
    {
        $this->call([
            RolesTableSeeder::class,
            UsersTableSeeder::class,
            CategoriesTableSeeder::class,
            ProductsTableSeeder::class,
            ProductVariantsTableSeeder::class,
            PromotionsTableSeeder::class,
            ProductPromotionsTableSeeder::class,
            ImageProductsTableSeeder::class,
            OrdersTableSeeder::class,
            OrderDetailsTableSeeder::class,
        ]);
    }
}
