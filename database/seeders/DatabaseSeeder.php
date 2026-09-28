<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->createAdminUser();
        $this->createCategories();
        $this->createProducts();
        $this->markFeaturedProducts();
    }

    private function createAdminUser(): void
    {
        User::query()->updateOrCreate(
            ['email' => 'admin@fathercarebakery.com'],
            [
                'name' => 'Admin Father Care',
                'password' => 'password',
                'role' => UserRole::ADMIN,
                'phone' => '08139502961',
                'address' => config('bakery.address'),
            ]
        );
    }

    private function createCategories(): void
    {
        $categories = [
            ['name' => 'Bread', 'slug' => 'bread', 'description' => 'Freshly baked bread varieties'],
            ['name' => 'Cakes', 'slug' => 'cakes', 'description' => 'Celebration and dessert cakes'],
            ['name' => 'Pastries', 'slug' => 'pastries', 'description' => 'Flaky and buttery pastries'],
            ['name' => 'Buns', 'slug' => 'buns', 'description' => 'Soft and sweet buns'],
            ['name' => 'Specials', 'slug' => 'specials', 'description' => 'Daily specials and seasonal treats'],
            ['name' => 'Combos', 'slug' => 'combos', 'description' => 'Value bundles for parties and celebrations'],
        ];

        foreach ($categories as $cat) {
            Category::query()->updateOrCreate(
                ['slug' => $cat['slug']],
                array_merge($cat, ['is_active' => true])
            );
        }
    }

    private function createProducts(): void
    {
        $categoryIds = Category::query()->pluck('id', 'name');

        $products = [
            ['category' => 'Bread', 'name' => 'White Bread', 'price' => 500, 'stock' => 20, 'description' => 'Soft, fluffy white loaf baked fresh every morning.'],
            ['category' => 'Bread', 'name' => 'Whole Wheat Bread', 'price' => 650, 'stock' => 15, 'description' => 'Hearty whole wheat loaf with a rich, nutty flavour.'],
            ['category' => 'Bread', 'name' => 'Sourdough Bread', 'price' => 800, 'stock' => 10, 'description' => 'Rustic sourdough with a crackled crust and tangy crumb.'],
            ['category' => 'Cakes', 'name' => 'Vanilla Cake', 'price' => 1500, 'stock' => 5, 'description' => 'Light vanilla sponge finished with smooth cream frosting.'],
            ['category' => 'Cakes', 'name' => 'Chocolate Cake', 'price' => 1800, 'stock' => 4, 'description' => 'Moist chocolate layers covered in glossy ganache.'],
            ['category' => 'Cakes', 'name' => 'Red Velvet Cake', 'price' => 2000, 'stock' => 3, 'description' => 'Classic red velvet with cream cheese frosting.'],
            ['category' => 'Pastries', 'name' => 'Croissant', 'price' => 400, 'stock' => 25, 'description' => 'Buttery, flaky croissant baked golden brown.'],
            ['category' => 'Pastries', 'name' => 'Danish Pastry', 'price' => 450, 'stock' => 20, 'description' => 'Fruit-filled Danish with a light icing drizzle.'],
            ['category' => 'Pastries', 'name' => 'Pain au Chocolat', 'price' => 500, 'stock' => 18, 'description' => 'Laminated pastry wrapped around dark chocolate bars.'],
            ['category' => 'Buns', 'name' => 'Sugar Bun', 'price' => 300, 'stock' => 30, 'description' => 'Soft bun topped with sparkling sugar crystals.'],
            ['category' => 'Buns', 'name' => 'Coconut Bun', 'price' => 350, 'stock' => 25, 'description' => 'Golden bun finished with toasted coconut flakes.'],
            ['category' => 'Buns', 'name' => 'Cinnamon Bun', 'price' => 400, 'stock' => 20, 'description' => 'Swirled cinnamon bun with a sweet icing glaze.'],
            ['category' => 'Specials', 'name' => 'Meat Pie', 'price' => 550, 'stock' => 10, 'description' => 'Flaky pastry filled with seasoned minced meat and potato.'],
            ['category' => 'Specials', 'name' => 'Sausage Roll', 'price' => 500, 'stock' => 12, 'description' => 'Golden puff pastry wrapped around a juicy sausage.'],
            ['category' => 'Specials', 'name' => 'Egg Roll', 'price' => 450, 'stock' => 15, 'description' => 'Crisp fried dough wrapped around a whole boiled egg.'],
            ['category' => 'Combos', 'name' => 'Party Combo', 'price' => 6499, 'compare_at_price' => 8500, 'stock' => 10, 'description' => "1 Cake (Any 1lb)\n6 Cupcakes\n2 Drinks"],
            ['category' => 'Combos', 'name' => 'Family Combo', 'price' => 8999, 'compare_at_price' => 12500, 'stock' => 8, 'description' => "1 Cake (Any 1.5lb)\n12 Cupcakes\n1 Pastry Box"],
            ['category' => 'Combos', 'name' => 'Celebration Combo', 'price' => 12999, 'compare_at_price' => 18000, 'stock' => 6, 'description' => "1 Cake (Any 2lb)\n12 Cupcakes\n2 Pastry Boxes\n2 Drinks"],
        ];

        foreach ($products as $product) {
            $slug = Str::slug($product['name']);
            $basePrice = $product['price'];
            $image = $this->storeProductImage($slug);

            $attributes = [
                'category_id' => $categoryIds[$product['category']],
                'name' => $product['name'],
                'description' => $product['description'],
                'price' => $basePrice,
                'compare_at_price' => $product['compare_at_price'] ?? null,
                'cost_price' => round($basePrice * 0.6, 2),
                'stock_quantity' => $product['stock'],
                'low_stock_threshold' => 3,
                'is_active' => true,
            ];

            if ($image !== null) {
                $attributes['image'] = $image;
            }

            Product::query()->updateOrCreate(
                ['slug' => $slug],
                $attributes
            );
        }
    }

    private function markFeaturedProducts(): void
    {
        $featuredNames = [
            'White Bread',
            'Whole Wheat Bread',
            'Chocolate Cake',
            'Danish Pastry',
            'Sugar Bun',
            'Meat Pie',
        ];

        Product::query()->whereIn('name', $featuredNames)->update(['is_featured' => true]);
    }

    private function storeProductImage(string $slug): ?string
    {
        $filename = $slug.'.png';
        $source = database_path('seeders/assets/products/'.$filename);

        if (! is_file($source)) {
            return null;
        }

        $destination = 'products/'.$filename;

        Storage::disk('public')->makeDirectory('products');
        Storage::disk('public')->put($destination, (string) file_get_contents($source));

        return $destination;
    }
}
