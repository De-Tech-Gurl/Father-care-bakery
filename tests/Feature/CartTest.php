<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_renders_the_add_to_cart_route(): void
    {
        $this->get(route('customer.home'))
            ->assertOk()
            ->assertSee(route('customer.cart.add'), false);
    }

    public function test_guest_can_add_an_active_product_to_the_cart(): void
    {
        $product = Product::factory()->create();

        $this->postJson(route('customer.cart.add'), [
            'product_id' => $product->id,
            'quantity' => 1,
        ])
            ->assertOk()
            ->assertJsonPath('message', 'Added to cart.')
            ->assertJsonPath('cart_count', 1);

        $this->assertSame(1, session('cart')[$product->id]['quantity']);
    }

    public function test_adding_the_same_product_increments_quantity(): void
    {
        $product = Product::factory()->create(['stock_quantity' => 10]);

        $this->postJson(route('customer.cart.add'), [
            'product_id' => $product->id,
            'quantity' => 2,
        ])->assertOk();

        $this->postJson(route('customer.cart.add'), [
            'product_id' => $product->id,
            'quantity' => 1,
        ])
            ->assertOk()
            ->assertJsonPath('cart_count', 3);

        $this->assertSame(3, session('cart')[$product->id]['quantity']);
    }

    public function test_cart_accepts_quantities_above_two_up_to_available_stock(): void
    {
        $product = Product::factory()->create(['stock_quantity' => 10]);

        $this->postJson(route('customer.cart.add'), [
            'product_id' => $product->id,
            'quantity' => 5,
        ])
            ->assertOk()
            ->assertJsonPath('cart_count', 5);

        $this->patchJson(route('customer.cart.update', $product), [
            'quantity' => 8,
        ])
            ->assertOk()
            ->assertJsonPath('quantity', 8)
            ->assertJsonPath('stock_quantity', 10);

        $this->patchJson(route('customer.cart.update', $product), [
            'quantity' => 11,
        ])->assertUnprocessable()
            ->assertJsonValidationErrors('quantity');

        $this->assertSame(8, session('cart')[$product->id]['quantity']);
    }

    public function test_inactive_product_cannot_be_added_to_the_cart(): void
    {
        $product = Product::factory()->inactive()->create();

        $this->postJson(route('customer.cart.add'), [
            'product_id' => $product->id,
            'quantity' => 1,
        ])->assertUnprocessable();

        $this->assertEmpty(session('cart', []));
    }

    public function test_add_fails_when_quantity_exceeds_stock(): void
    {
        $product = Product::factory()->create(['stock_quantity' => 2]);

        $this->postJson(route('customer.cart.add'), [
            'product_id' => $product->id,
            'quantity' => 3,
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('quantity');
    }

    public function test_cart_page_shows_added_products(): void
    {
        $product = Product::factory()->create(['name' => 'Meat Pie']);

        $this->postJson(route('customer.cart.add'), [
            'product_id' => $product->id,
            'quantity' => 1,
        ])->assertOk();

        $this->get(route('customer.cart.index'))
            ->assertOk()
            ->assertSee('Meat Pie');
    }

    public function test_guest_can_change_cart_quantity(): void
    {
        $product = Product::factory()->create(['stock_quantity' => 10]);

        $this->postJson(route('customer.cart.add'), [
            'product_id' => $product->id,
            'quantity' => 1,
        ])->assertOk();

        $this->from(route('customer.cart.index'))
            ->patch(route('customer.cart.update', $product), [
                'quantity' => 3,
            ])
            ->assertRedirect(route('customer.cart.index'));

        $this->assertSame(3, session('cart')[$product->id]['quantity']);
    }

    public function test_product_id_is_required(): void
    {
        $this->postJson(route('customer.cart.add'), [
            'quantity' => 1,
        ])->assertUnprocessable()
            ->assertJsonValidationErrors('product_id');
    }
}
