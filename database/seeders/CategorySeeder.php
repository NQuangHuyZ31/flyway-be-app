<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //

        $data = [
            ['name' => 'Electronics', 'slug' => 'electronics', 'is_active' => true],
            ['name' => 'Books', 'slug' => 'books', 'is_active' => true],
            ['name' => 'Clothing', 'slug' => 'clothing', 'is_active' => true],
            ['name' => 'Home & Kitchen', 'slug' => 'home-kitchen', 'is_active' => true],
            ['name' => 'Sports & Outdoors', 'slug' => 'sports-outdoors', 'is_active' => true],
        ];

        foreach ($data as $category) {
            \App\Models\Category::create($category);
        }
    }
}
