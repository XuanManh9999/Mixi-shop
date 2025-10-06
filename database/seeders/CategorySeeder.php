<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Hamburger',
                'slug' => 'hamburger',
                'position' => 1,
                'is_active' => 1,
            ],
            [
                'name' => 'Pizza',
                'slug' => 'pizza',
                'position' => 2,
                'is_active' => 1,
            ],
            [
                'name' => 'Gà Rán',
                'slug' => 'ga-ran',
                'position' => 3,
                'is_active' => 1,
            ],
            [
                'name' => 'Nước Uống',
                'slug' => 'nuoc-uong',
                'position' => 4,
                'is_active' => 1,
            ],
            [
                'name' => 'Tráng Miệng',
                'slug' => 'trang-mieng',
                'position' => 5,
                'is_active' => 1,
            ],
            [
                'name' => 'Combo',
                'slug' => 'combo',
                'position' => 0,
                'is_active' => 1,
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}