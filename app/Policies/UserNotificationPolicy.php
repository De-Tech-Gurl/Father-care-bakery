<?php

namespace App\Policies;

use App\Models\User;
use App\Models\UserNotification;

class UserNotificationPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, UserNotification $userNotification): bool
    {
        return $this->owns($user, $userNotification);
    }

    public function delete(User $user, UserNotification $userNotification): bool
    {
        return $this->owns($user, $userNotification);
    }

    private function owns(User $user, UserNotification $notification): bool
    {
        return $notification->notifiable_type === $user->getMorphClass()
            && (string) $notification->notifiable_id === (string) $user->getKey();
    }
}
