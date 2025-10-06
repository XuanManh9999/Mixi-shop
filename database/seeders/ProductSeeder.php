<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $hamburgerCategory = Category::where('slug', 'hamburger')->first();
        $pizzaCategory = Category::where('slug', 'pizza')->first();
        $chickenCategory = Category::where('slug', 'ga-ran')->first();
        $drinkCategory = Category::where('slug', 'nuoc-uong')->first();
        $comboCategory = Category::where('slug', 'combo')->first();

        $products = [
            // Hamburgers
            [
                'category_id' => $hamburgerCategory->id,
                'name' => 'Big Mixi Burger',
                'slug' => 'big-mixi-burger',
                'sku' => 'BMB001',
                'description' => 'Hamburger bò đặc biệt với phô mai, rau xanh tươi ngon',
                'price' => 85000,
                'stock_qty' => 50,
                'is_active' => 1,
            ],
            [
                'category_id' => $hamburgerCategory->id,
                'name' => 'Chicken Deluxe Burger',
                'slug' => 'chicken-deluxe-burger',
                'sku' => 'CDB002',
                'description' => 'Hamburger gà giòn với sốt đặc biệt',
                'price' => 75000,
                'stock_qty' => 30,
                'is_active' => 1,
            ],
            [
                'category_id' => $hamburgerCategory->id,
                'name' => 'Fish Burger Classic',
                'slug' => 'fish-burger-classic',
                'sku' => 'FBC003',
                'description' => 'Hamburger cá tươi ngon với sốt tartar',
                'price' => 70000,
                'stock_qty' => 25,
                'is_active' => 1,
            ],

            // Pizzas
            [
                'category_id' => $pizzaCategory->id,
                'name' => 'Pizza Hải Sản Đặc Biệt',
                'slug' => 'pizza-hai-san-dac-biet',
                'sku' => 'PHS004',
                'description' => 'Pizza với tôm, mực, cua và phô mai mozzarella',
                'price' => 120000,
                'stock_qty' => 20,
                'is_active' => 1,
            ],
            [
                'category_id' => $pizzaCategory->id,
                'name' => 'Pizza Pepperoni',
                'slug' => 'pizza-pepperoni',
                'sku' => 'PPP005',
                'description' => 'Pizza truyền thống với xúc xích pepperoni',
                'price' => 95000,
                'stock_qty' => 35,
                'is_active' => 1,
            ],

            // Gà rán
            [
                'category_id' => $chickenCategory->id,
                'name' => 'Gà Rán Giòn Cay',
                'slug' => 'ga-ran-gion-cay',
                'sku' => 'GRC006',
                'description' => '6 miếng gà rán giòn với gia vị cay đặc biệt',
                'price' => 110000,
                'stock_qty' => 40,
                'is_active' => 1,
            ],
            [
                'category_id' => $chickenCategory->id,
                'name' => 'Cánh Gà BBQ',
                'slug' => 'canh-ga-bbq',
                'sku' => 'CGB007',
                'description' => '8 cánh gà nướng BBQ thơm ngon',
                'price' => 85000,
                'stock_qty' => 30,
                'is_active' => 1,
            ],

            // Nước uống
            [
                'category_id' => $drinkCategory->id,
                'name' => 'Coca Cola',
                'slug' => 'coca-cola',
                'sku' => 'COC008',
                'description' => 'Nước ngọt Coca Cola 330ml',
                'price' => 15000,
                'stock_qty' => 100,
                'is_active' => 1,
            ],
            [
                'category_id' => $drinkCategory->id,
                'name' => 'Trà Đào Cam Sả',
                'slug' => 'tra-dao-cam-sa',
                'sku' => 'TDC009',
                'description' => 'Trà đào cam sả tươi mát',
                'price' => 25000,
                'stock_qty' => 50,
                'is_active' => 1,
            ],

            // Combo
            [
                'category_id' => $comboCategory->id,
                'name' => 'Combo Big Mixi',
                'slug' => 'combo-big-mixi',
                'sku' => 'CBM010',
                'description' => 'Big Mixi Burger + Khoai tây chiên + Coca Cola',
                'price' => 120000,
                'stock_qty' => 25,
                'is_active' => 1,
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}