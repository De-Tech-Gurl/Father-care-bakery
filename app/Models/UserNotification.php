<?php

namespace App\Models;

use Database\Factories\UserNotificationFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\DatabaseNotification;

class UserNotification extends DatabaseNotification
{
    /** @use HasFactory<UserNotificationFactory> */
    use HasFactory;

    protected static function newFactory(): UserNotificationFactory
    {
        return UserNotificationFactory::new();
    }
}
