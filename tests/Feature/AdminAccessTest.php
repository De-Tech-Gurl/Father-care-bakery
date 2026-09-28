<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_cannot_view_the_admin_dashboard(): void
    {
        $this->get(route('admin.dashboard'))
            ->assertRedirect(route('login'));
    }

    public function test_customers_cannot_view_the_admin_dashboard(): void
    {
        $customer = User::factory()->create();

        $this->actingAs($customer)
            ->get(route('admin.dashboard'))
            ->assertForbidden();
    }

    public function test_admins_can_view_the_dashboard_and_create_products(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Dashboard');

        $this->actingAs($admin)
            ->post(route('admin.products.store'), [
                'name' => 'Coconut Bun',
                'description' => 'Soft coconut bun',
                'price' => 350,
                'stock_quantity' => 20,
                'low_stock_threshold' => 5,
                'is_active' => 1,
            ])
            ->assertRedirect(route('admin.products.index'));

        $this->assertDatabaseHas('products', [
            'name' => 'Coconut Bun',
            'price' => 350,
        ]);
    }

    public function test_admins_are_redirected_to_the_dashboard_after_login(): void
    {
        $admin = User::factory()->admin()->create();

        $this->post('/login', [
            'email' => $admin->email,
            'password' => 'password',
        ])->assertRedirect(route('admin.dashboard'));
    }

    public function test_seeded_admin_can_login(): void
    {
        $this->seed();

        $this->post('/login', [
            'email' => 'admin@fathercarebakery.com',
            'password' => 'password',
        ])
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('admin.dashboard'));
    }

    public function test_staff_can_open_inventory(): void
    {
        $staff = User::factory()->staff()->create();
        Product::factory()->create();

        $this->actingAs($staff)
            ->get(route('admin.inventory.index'))
            ->assertOk();
    }
}
