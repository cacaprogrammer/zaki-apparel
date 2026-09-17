<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'School Uniform', 'slug' => 'school-uniform', 'description' => 'Seragam sekolah nyaman untuk anak-anak.'],
            ['name' => 'Flannel Shirt', 'slug' => 'flannel-shirt', 'description' => 'Kemeja flanel lembut untuk sehari-hari.'],
            ['name' => 'Classic Shirt', 'slug' => 'classic-shirt', 'description' => 'Kemeja klasik yang serbaguna.'],
            ['name' => 'Striped Shirt', 'slug' => 'striped-shirt', 'description' => 'Kemeja garis-garis yang ceria.'],
        ];

        $categoryIds = [];
        foreach ($categories as $cat) {
            $categoryIds[$cat['slug']] = Category::updateOrCreate(['slug' => $cat['slug']], $cat)->id;
        }

        $products = [
            // School Uniform
            ['name' => 'Kids School Shirt', 'slug' => 'kids-school-shirt', 'category' => 'school-uniform', 'price' => 125000, 'sizes' => ['S', 'M', 'L'], 'image' => '/images/products/school-shirt.jpg', 'badge' => 'New'],
            ['name' => 'Long-Sleeve School Shirt', 'slug' => 'long-sleeve-school-shirt', 'category' => 'school-uniform', 'price' => 135000, 'sizes' => ['M', 'L', 'XL'], 'image' => '/images/products/school-shirt.jpg', 'badge' => null],
            ['name' => 'School Polo Shirt', 'slug' => 'school-polo-shirt', 'category' => 'school-uniform', 'price' => 119000, 'sizes' => ['S', 'M'], 'image' => '/images/products/school-shirt.jpg', 'badge' => null],
            ['name' => 'Navy School Shirt', 'slug' => 'school-shirt-navy', 'category' => 'school-uniform', 'price' => 129000, 'sizes' => ['L', 'XL'], 'image' => '/images/products/school-shirt.jpg', 'badge' => null],
            ['name' => 'White School Shirt', 'slug' => 'school-shirt-white', 'category' => 'school-uniform', 'price' => 115000, 'sizes' => ['S', 'M', 'L'], 'image' => '/images/products/school-shirt.jpg', 'badge' => 'New'],
            ['name' => 'School Vest', 'slug' => 'school-vest', 'category' => 'school-uniform', 'price' => 149000, 'sizes' => ['M', 'L'], 'image' => '/images/products/school-shirt.jpg', 'badge' => null],

            // Flannel Shirt
            ['name' => 'Kids Flannel Shirt', 'slug' => 'kids-flannel-shirt', 'category' => 'flannel-shirt', 'price' => 149000, 'sizes' => ['S', 'M', 'L', 'XL'], 'image' => '/images/products/flannel-shirt.jpg', 'badge' => 'New'],
            ['name' => 'Clay Flannel Shirt', 'slug' => 'clay-flannel-shirt', 'category' => 'flannel-shirt', 'price' => 149000, 'sizes' => ['M', 'L'], 'image' => '/images/products/flannel-2.jpg', 'badge' => null],
            ['name' => 'Golden Sand Flannel Shirt', 'slug' => 'golden-sand-flannel-shirt', 'category' => 'flannel-shirt', 'price' => 145000, 'sizes' => ['S', 'M'], 'image' => '/images/products/flannel-3.jpg', 'badge' => null],
            ['name' => 'Gray Flannel Shirt', 'slug' => 'gray-flannel-shirt', 'category' => 'flannel-shirt', 'price' => 139000, 'sizes' => ['L', 'XL'], 'image' => '/images/products/flannel-1.jpg', 'badge' => null],

            // Classic Shirt
            ['name' => 'Navy Classic Shirt', 'slug' => 'navy-classic-shirt', 'category' => 'classic-shirt', 'price' => 159000, 'sizes' => ['S', 'M', 'L'], 'image' => '/images/products/navy-classic.jpg', 'badge' => null],
            ['name' => 'Sage Green Classic Shirt', 'slug' => 'sage-green-classic-shirt', 'category' => 'classic-shirt', 'price' => 135000, 'sizes' => ['M', 'L', 'XL'], 'image' => '/images/products/flannel-1.jpg', 'badge' => null],
            ['name' => 'Sunny Yellow Tee', 'slug' => 'sunny-yellow-tee', 'category' => 'classic-shirt', 'price' => 99000, 'sizes' => ['S', 'M'], 'image' => '/images/products/flannel-shirt.jpg', 'badge' => 'New'],
            ['name' => 'Classic Khaki Shorts', 'slug' => 'classic-khaki-shorts', 'category' => 'classic-shirt', 'price' => 115000, 'sizes' => ['M', 'L'], 'image' => '/images/products/flannel-1.jpg', 'badge' => null],

            // Striped Shirt
            ['name' => 'Blossom Pink Striped Shirt', 'slug' => 'blossom-pink-striped-shirt', 'category' => 'striped-shirt', 'price' => 129000, 'sizes' => ['S', 'M'], 'image' => '/images/products/striped-shirt.jpg', 'badge' => 'Sale'],
            ['name' => 'Classic White Striped Shirt', 'slug' => 'classic-white-striped-shirt', 'category' => 'striped-shirt', 'price' => 119000, 'sizes' => ['M', 'L'], 'image' => '/images/products/striped-shirt.jpg', 'badge' => null],
        ];

        foreach ($products as $p) {
            Product::updateOrCreate(
                ['slug' => $p['slug']],
                [
                    'category_id' => $categoryIds[$p['category']],
                    'name' => $p['name'],
                    'description' => 'Deskripsi produk ' . $p['name'] . ' akan diperbarui oleh admin.',
                    'price' => $p['price'],
                    'stock' => rand(10, 50),
                    'images' => [$p['image']],
                    'colors' => [],
                    'sizes' => $p['sizes'],
                    'badge' => $p['badge'],
                    'rating' => round(rand(40, 50) / 10, 1),
                    'reviews_count' => rand(5, 130),
                ]
            );
        }
    }
}