<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\UserNotification;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<UserNotification>
 */
class UserNotificationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id' => (string) Str::uuid(),
            'type' => 'App\\Notifications\\BakeryNotification',
            'notifiable_type' => User::class,
            'notifiable_id' => User::factory(),
            'data' => [
                'type' => 'order.status_changed',
                'title' => 'Order status changed',
                'message' => 'Your order is being prepared.',
                'url' => '/orders',
                'icon' => 'ph-receipt',
                'context' => [],
            ],
            'read_at' => null,
        ];
    }
}
