<?php

namespace App\Models;

use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'address',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
            'notification_preferences' => 'array',
        ];
    }

    public function notifications(): MorphMany
    {
        return $this->morphMany(UserNotification::class, 'notifiable')->latest();
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === UserRole::ADMIN;
    }

    public function isStaff(): bool
    {
        return $this->role === UserRole::STAFF;
    }

    public function isCustomer(): bool
    {
        return $this->role === UserRole::CUSTOMER;
    }

    /**
     * @return list<string>
     */
    public function notificationChannelsFor(string $type): array
    {
        $preferences = $this->notification_preferences ?? [];
        $typePreferences = $preferences[$type] ?? [];
        $typeConfiguration = config('notifications.types', [])[$type] ?? [];

        return collect(['database', 'mail'])
            ->filter(function (string $channel) use ($typePreferences, $typeConfiguration): bool {
                $default = $channel === 'database'
                    ? true
                    : (bool) ($typeConfiguration['mail_default'] ?? false);

                return (bool) ($typePreferences[$channel] ?? $default);
            })
            ->values()
            ->all();
    }
}
