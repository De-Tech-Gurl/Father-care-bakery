<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_update_and_delete_a_product(): void
    {
        Storage::fake('public');

        $admin = User::factory()->admin()->create();
        $category = Category::factory()->create(['name' => 'Buns']);

        $this->actingAs($admin)
            ->post(route('admin.products.store'), [
                'name' => 'Coconut Bun',
                'category_id' => $category->id,
                'description' => 'Soft coconut bun',
                'price' => 350,
                'cost_price' => 200,
                'stock_quantity' => 20,
                'low_stock_threshold' => 5,
                'is_active' => 1,
                'is_featured' => 1,
                'image' => UploadedFile::fake()->image('coconut-bun.jpg', 400, 400),
            ])
            ->assertRedirect(route('admin.products.index'));

        $product = Product::query()->where('name', 'Coconut Bun')->first();

        $this->assertNotNull($product);
        $this->assertTrue($product->is_active);
        $this->assertTrue($product->is_featured);
        $this->assertNotNull($product->image);
        Storage::disk('public')->assertExists($product->image);

        $this->get(route('customer.home'))
            ->assertOk()
            ->assertSee('Coconut Bun')
            ->assertSee($product->imageUrl, false);

        $this->actingAs($admin)
            ->put(route('admin.products.update', $product), [
                'name' => 'Toasted Coconut Bun',
                'category_id' => $category->id,
                'description' => 'Toasted coconut bun',
                'price' => 400,
                'cost_price' => 220,
                'stock_quantity' => 18,
                'low_stock_threshold' => 4,
                'is_active' => 1,
                'is_featured' => 1,
            ])
            ->assertRedirect(route('admin.products.index'));

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Toasted Coconut Bun',
            'price' => 400,
        ]);

        $this->get(route('customer.products.index'))
            ->assertOk()
            ->assertSee('Toasted Coconut Bun');

        $imagePath = $product->fresh()->image;

        $this->actingAs($admin)
            ->delete(route('admin.products.destroy', $product))
            ->assertRedirect(route('admin.products.index'));

        $this->assertDatabaseMissing('products', ['id' => $product->id]);
        Storage::disk('public')->assertMissing($imagePath);
    }

    public function test_guests_cannot_manage_products(): void
    {
        $product = Product::factory()->create();

        $this->get(route('admin.products.index'))->assertRedirect(route('login'));
        $this->post(route('admin.products.store'), [
            'name' => 'Hidden Pie',
            'price' => 500,
            'stock_quantity' => 4,
        ])->assertRedirect(route('login'));
        $this->delete(route('admin.products.destroy', $product))->assertRedirect(route('login'));
    }
}
