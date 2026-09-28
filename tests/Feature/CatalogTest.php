<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CatalogTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_can_browse_and_search_products(): void
    {
        $bread = Category::factory()->create(['name' => 'Bread']);
        $cakes = Category::factory()->create(['name' => 'Cakes']);

        Product::factory()->create([
            'name' => 'White Bread',
            'category_id' => $bread->id,
        ]);
        Product::factory()->create([
            'name' => 'Chocolate Cake',
            'category_id' => $cakes->id,
        ]);

        $this->get(route('customer.products.index'))
            ->assertOk()
            ->assertSee('White Bread')
            ->assertSee('Chocolate Cake');

        $this->get(route('customer.products.index', ['category' => $bread->id]))
            ->assertOk()
            ->assertSee('White Bread')
            ->assertDontSee('Chocolate Cake');

        $this->get(route('customer.products.index', ['q' => 'Chocolate']))
            ->assertOk()
            ->assertSee('Chocolate Cake')
            ->assertDontSee('White Bread');
    }

    public function test_guests_can_view_an_active_product(): void
    {
        $product = Product::factory()->create(['name' => 'Sugar Bun']);

        $this->get(route('customer.products.show', $product))
            ->assertOk()
            ->assertSee('Sugar Bun');
    }

    public function test_inactive_products_are_not_shown(): void
    {
        $product = Product::factory()->inactive()->create();

        $this->get(route('customer.products.show', $product))->assertNotFound();
    }

    public function test_home_hero_invites_guests_to_shop_and_order(): void
    {
        $this->get(route('customer.home'))
            ->assertOk()
            ->assertSee('Warm from the oven')
            ->assertSee('Made for you')
            ->assertSee('Shop the bake')
            ->assertSee(route('customer.products.index'), false)
            ->assertSee('wa.me/2348139502961', false)
            ->assertSee('images/hero-cake.png', false)
            ->assertSee('images/hero-bread.png', false)
            ->assertSee('images/hero-pastries.png', false)
            ->assertSee('images/hero-buns.png', false)
            ->assertSee('images/hero-specials.png', false)
            ->assertSee('heroShowcase', false)
            ->assertSee('Bakery products showcase');
    }

    public function test_home_combos_are_shoppable_and_view_all_opens_the_combos_shop(): void
    {
        $combos = Category::factory()->create(['name' => 'Combos', 'slug' => 'combos']);
        $bread = Category::factory()->create(['name' => 'Bread', 'slug' => 'bread']);

        $party = Product::factory()->create([
            'name' => 'Party Combo',
            'category_id' => $combos->id,
            'price' => 6499,
            'compare_at_price' => 8500,
            'description' => "1 Cake (Any 1lb)\n6 Cupcakes\n2 Drinks",
        ]);
        Product::factory()->create([
            'name' => 'White Bread',
            'category_id' => $bread->id,
        ]);

        $this->get(route('customer.home'))
            ->assertOk()
            ->assertSee('Party Combo')
            ->assertSee('1 Cake (Any 1lb)')
            ->assertSee('SAVE 24%')
            ->assertSee(route('customer.products.index', ['category' => 'combos']), false)
            ->assertSee('data-id="'.$party->id.'"', false);

        $this->get(route('customer.products.index', ['category' => 'combos']))
            ->assertOk()
            ->assertSee('Party Combo')
            ->assertDontSee('White Bread');

        $this->postJson(route('customer.cart.add'), [
            'product_id' => $party->id,
            'quantity' => 1,
        ])
            ->assertOk()
            ->assertJsonPath('cart_count', 1);
    }
}
