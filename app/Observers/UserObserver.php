<?php

namespace App\Observers;

use App\Events\NotificationRequested;
use App\Models\User;

class UserObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        event(new NotificationRequested(
            'user.registered',
            'New user registered',
            $user->name.' created an account.',
            'staff',
            url: route('admin.customers.index'),
            icon: 'ph-user-plus',
            context: ['user_id' => $user->id],
        ));
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        if ($user->wasChanged('role')) {
            event(new NotificationRequested(
                'user.role_changed',
                'Your account role changed',
                'Your account permissions have been updated.',
                'user',
                $user->id,
                route('customer.home'),
                'ph-shield-check',
            ));

            event(new NotificationRequested(
                'user.role_changed',
                'User role changed',
                $user->name.' account role was changed.',
                'staff',
                url: route('admin.customers.index'),
                icon: 'ph-shield-check',
                context: ['user_id' => $user->id],
            ));
        }

        if ($user->wasChanged('password')) {
            event(new NotificationRequested(
                'user.password_changed',
                'Password changed',
                'Your account password was changed.',
                'user',
                $user->id,
                route('customer.home'),
                'ph-lock-key',
            ));
        }
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        //
    }

    /**
     * Handle the User "restored" event.
     */
    public function restored(User $user): void
    {
        //
    }

    /**
     * Handle the User "force deleted" event.
     */
    public function forceDeleted(User $user): void
    {
        //
    }
}
