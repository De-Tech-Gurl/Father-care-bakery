<?php

namespace Database\Factories;

use App\Enums\DeliveryType;
use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'order_number' => 'FCB-'.now()->format('Ymd').'-'.strtoupper(fake()->unique()->bothify('????')),
            'total_amount' => fake()->randomFloat(2, 500, 8000),
            'status' => OrderStatus::PENDING,
            'delivery_type' => DeliveryType::PICKUP,
            'delivery_address' => null,
            'phone' => '08012345678',
            'notes' => null,
            'payment_method' => PaymentMethod::BANK_TRANSFER,
            'payment_status' => PaymentStatus::AWAITING_PAYMENT,
            'payment_reference' => null,
            'paid_at' => null,
        ];
    }
}
