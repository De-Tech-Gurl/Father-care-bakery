<?php

namespace App\Listeners;

use App\Enums\UserRole;
use App\Events\NotificationRequested;
use App\Models\User;
use App\Notifications\BakeryNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Throwable;

class DispatchNotification
{
    public function handle(NotificationRequested $event): void
    {
        if (! array_key_exists($event->type, config('notifications.types', []))) {
            return;
        }

        if ($event->audience === 'user' && $event->recipientId !== null) {
            $user = User::query()->find($event->recipientId);

            if ($user) {
                $this->sendTo($user, $event);
            }

            return;
        }

        if ($event->audience !== 'staff') {
            return;
        }

        User::query()
            ->whereIn('role', [UserRole::ADMIN->value, UserRole::STAFF->value])
            ->orderBy('id')
            ->chunkById(100, function ($users) use ($event): void {
                foreach ($users as $user) {
                    $this->sendTo($user, $event);
                }
            });
    }

    private function sendTo(User $user, NotificationRequested $event): void
    {
        try {
            $channels = $user->notificationChannelsFor($event->type);
            $notification = new BakeryNotification(
                $event->type,
                $event->title,
                $event->message,
                $event->url,
                $event->icon,
                $event->context,
            );

            if (in_array('database', $channels, true)) {
                Notification::sendNow($user, $notification, ['database']);
            }

            if (in_array('mail', $channels, true)) {
                $user->notify($notification->mailOnly()->afterCommit());
            }
        } catch (Throwable $exception) {
            Log::error('Unable to deliver notification.', [
                'notification_type' => $event->type,
                'user_id' => $user->id,
                'exception' => $exception,
            ]);
        }
    }
}
