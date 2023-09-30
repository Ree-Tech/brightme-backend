<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductReview;
use Illuminate\Database\Seeder;
use App\Models\ProductCategory;
use App\Models\ProductVariation;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Product Categories
        $productCategories = [
            'Special Offers',
            'Popular Products',
            'New Arrivals',
        ];
        $slugCategories = [
            'special-offers',
            'popular-products',
            'new-arrivals',
        ];
        foreach ($productCategories as $key => $productCategory) {
            ProductCategory::create([
                'name' => $productCategory,
                'slug' => $slugCategories[$key],
            ]);
        }

        // Product
        $product1 = Product::create([
            'name' => 'Travel Pack - True Brotherhod',
            'description' => 'Nourishing beard, energizin and brightening face, hair and body wash, Leaflet',
            'usage' => 'For Daily Usage',
            'discount' => '10',
            'slug' => 'travel-pack-true',
            'product_category_id' => 1
        ]);

        $product2 = Product::create([
            'name' => 'Natural Daily Aloe Hydramild Gel',
            'description' => 'A multi-functional gel containing natural Aloe vera that provides extra moisture with a soothing effect and cools dry and reddened skin from the sun. Its unique, light, and non-sticky formula works as an anti-irritant, anti-inflammatory, as a soothing agent for sunburned skin, and helps increase skin resistance (skin barrier)',
            'usage' => 'Moisturizer Apply evenly on the skin that is dry, itchy or red. Can be applied on the face, body, feet, hands and hair.',
            'discount' => '10',
            'slug' => 'natural-daily-aloe',
            'product_category_id' => 2
        ]);

        $product3 = Product::create([
            'name' => 'Wardah White Secret 5in1',
            'description' => 'Nourishing beard, energizin and brightening face, hair and body wash, Leaflet',
            'usage' => 'For Daily Usage',
            'discount' => '10',
            'slug' => 'wardah-white-secret',
            'product_category_id' => 3
        ]);

        ProductVariation::create([
            'product_id' => $product1->id,
            'name' => '350ml',
            'price' => 70000
        ]);

        ProductVariation::create([
            'product_id' => $product1->id,
            'name' => '200ml',
            'price' => 30000
        ]);

        ProductVariation::create([
            'product_id' => $product2->id,
            'name' => '350ml',
            'price' => 50000
        ]);

        ProductVariation::create([
            'product_id' => $product3->id,
            'name' => '350ml',
            'price' => 60000
        ]);

        ProductReview::create([
            'product_id' => $product1->id,
            'name' => 'Beni Kurniaran',
            'review' => 'This product is good for my dry skin and maybe it hurts a little at first but it gets normal over time.',
            'stars' => 5
        ]);

        ProductReview::create([
            'product_id' => $product2->id,
            'name' => 'Beni Kurniaran',
            'review' => 'This product is good for my dry skin and maybe it hurts a little at first but it gets normal over time.',
            'stars' => 5
        ]);

        ProductReview::create([
            'product_id' => $product3->id,
            'name' => 'Beni Kurniaran',
            'review' => 'This product is good for my dry skin and maybe it hurts a little at first but it gets normal over time.',
            'stars' => 5
        ]);
    }
}
