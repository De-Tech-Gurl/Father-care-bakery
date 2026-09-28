<?php

namespace App\Models;

use App\Enums\DeliveryType;
use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'order_number',
        'total_amount',
        'status',
        'delivery_type',
        'delivery_address',
        'phone',
        'notes',
        'payment_method',
        'payment_status',
        'payment_reference',
        'payment_receipt',
        'paid_at',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'status' => OrderStatus::class,
        'delivery_type' => DeliveryType::class,
        'payment_method' => PaymentMethod::class,
        'payment_status' => PaymentStatus::class,
        'paid_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * @return Attribute<string|null, never>
     */
    protected function paymentReceiptUrl(): Attribute
    {
        return Attribute::get(function (): ?string {
            if (! filled($this->payment_receipt)) {
                return null;
            }

            return '/storage/'.ltrim((string) $this->payment_receipt, '/');
        });
    }

    public function canBeCancelled(): bool
    {
        return in_array($this->status, [OrderStatus::PENDING, OrderStatus::CONFIRMED], true)
            && $this->payment_status !== PaymentStatus::PAID;
    }

    public function needsOnlinePayment(): bool
    {
        return $this->payment_method?->usesPaystack() === true
            && in_array($this->payment_status, [PaymentStatus::AWAITING_PAYMENT, PaymentStatus::FAILED], true);
    }

    public function isAwaitingManualPayment(): bool
    {
        return in_array($this->payment_method, [PaymentMethod::BANK_TRANSFER, PaymentMethod::CASH_ON_DELIVERY], true)
            && $this->payment_status === PaymentStatus::AWAITING_PAYMENT;
    }
}
