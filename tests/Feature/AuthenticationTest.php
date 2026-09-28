<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_exposes_mobile_navigation_shortcuts(): void
    {
        $this->get(route('customer.home'))
            ->assertOk()
            ->assertSee('mobile-dock', false)
            ->assertSee('Open cart', false)
            ->assertSee('tel:+2348139502961', false);
    }

    public function test_pages_display_the_bakery_logo(): void
    {
        $this->get(route('customer.home'))
            ->assertOk()
            ->assertSee('images/logo.png', false)
            ->assertSee('Father Care Bakery', false);

        $this->get(route('login'))
            ->assertOk()
            ->assertSee('images/logo.png', false);
    }

    public function test_login_page_can_be_rendered(): void
    {
        $this->get(route('login'))->assertOk();
    }

    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $user = User::factory()->create();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('customer.home'));
    }

    public function test_users_cannot_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create();

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    public function test_users_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('logout'));

        $this->assertGuest();
        $response->assertRedirect(route('customer.home'));
    }
}
