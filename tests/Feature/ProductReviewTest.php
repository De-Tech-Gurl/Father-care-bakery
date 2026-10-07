<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\ProductReview;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductReviewTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_submit_a_product_rating_and_comment(): void
    {
        $customer = User::factory()->create();
        $product = Product::factory()->create();

        $this->actingAs($customer)
            ->post(route('customer.products.reviews.store', $product), [
                'rating' => 5,
                'comment' => 'Fresh and delicious.',
            ])
            ->assertRedirect(route('customer.products.show', $product).'#reviews');

        $this->assertDatabaseHas('product_reviews', [
            'product_id' => $product->id,
            'user_id' => $customer->id,
            'rating' => 5,
            'comment' => 'Fresh and delicious.',
        ]);

        $this->get(route('customer.products.show', $product))
            ->assertOk()
            ->assertSee('5.0 / 5')
            ->assertSee('Fresh and delicious.')
            ->assertSee($customer->name);
    }

    public function test_customer_can_update_their_existing_product_review(): void
    {
        $customer = User::factory()->create();
        $product = Product::factory()->create();
        $review = ProductReview::factory()
            ->for($product)
            ->for($customer)
            ->create(['rating' => 2, 'comment' => 'Could be better.']);

        $this->actingAs($customer)
            ->post(route('customer.products.reviews.store', $product), [
                'rating' => 4,
                'comment' => 'Much better this time.',
            ])
            ->assertRedirect(route('customer.products.show', $product).'#reviews');

        $this->assertDatabaseCount('product_reviews', 1);
        $this->assertDatabaseHas('product_reviews', [
            'id' => $review->id,
            'rating' => 4,
            'comment' => 'Much better this time.',
        ]);

        $this->get(route('customer.products.show', $product))
            ->assertOk()
            ->assertSee('Update your review')
            ->assertSee('Much better this time.');
    }

    public function test_guest_is_redirected_to_sign_in_before_submitting_a_review(): void
    {
        $product = Product::factory()->create();

        $this->post(route('customer.products.reviews.store', $product), [
            'rating' => 5,
            'comment' => 'Fresh and delicious.',
        ])->assertRedirect(route('login'));

        $this->assertDatabaseCount('product_reviews', 0);

        $this->get(route('customer.products.show', $product))
            ->assertOk()
            ->assertSee('Sign in')
            ->assertSee('No reviews yet');
    }

    public function test_non_customer_account_cannot_submit_a_product_review(): void
    {
        $staff = User::factory()->staff()->create();
        $product = Product::factory()->create();

        $this->actingAs($staff)
            ->post(route('customer.products.reviews.store', $product), [
                'rating' => 5,
                'comment' => 'Fresh and delicious.',
            ])
            ->assertForbidden();

        $this->assertDatabaseCount('product_reviews', 0);
    }

    public function test_invalid_rating_is_rejected_without_creating_a_review(): void
    {
        $customer = User::factory()->create();
        $product = Product::factory()->create();

        $this->actingAs($customer)
            ->from(route('customer.products.show', $product))
            ->post(route('customer.products.reviews.store', $product), [
                'rating' => 6,
                'comment' => 'Fresh and delicious.',
            ])
            ->assertSessionHasErrors('rating');

        $this->assertDatabaseCount('product_reviews', 0);
    }

    public function test_comment_is_required_for_a_product_review(): void
    {
        $customer = User::factory()->create();
        $product = Product::factory()->create();

        $this->actingAs($customer)
            ->from(route('customer.products.show', $product))
            ->post(route('customer.products.reviews.store', $product), [
                'rating' => 5,
                'comment' => '',
            ])
            ->assertSessionHasErrors('comment');

        $this->assertDatabaseCount('product_reviews', 0);
    }

    public function test_homepage_shows_review_averages_and_real_counts(): void
    {
        $product = Product::factory()->create(['is_featured' => true]);
        ProductReview::factory()->for($product)->create(['rating' => 5]);
        ProductReview::factory()->for($product)->create(['rating' => 4]);

        $this->get(route('customer.home'))
            ->assertOk()
            ->assertSee('4.5 (2)');
    }

    public function test_homepage_does_not_show_a_rating_for_unreviewed_products(): void
    {
        Product::factory()->create(['name' => 'Unreviewed Bun', 'is_featured' => true]);

        $this->get(route('customer.home'))
            ->assertOk()
            ->assertSee('Unreviewed Bun')
            ->assertSee('No reviews yet');
    }
}
