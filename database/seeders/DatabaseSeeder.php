<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Tạo user admin mặc định (nếu chưa có)
        User::firstOrCreate(
            ['email' => 'admin@mixishop.com'],
            [
                'name' => 'Admin',
                'password' => bcrypt('admin123'),
                'is_admin' => true,
            ]
        );

        // Tạo user thường để test (nếu chưa có)
        User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => bcrypt('password'),
                'is_admin' => false,
            ]
        );

        // Seed categories và products
        $this->call([
            CategorySeeder::class,
            ProductSeeder::class,
        ]);
    }
}
