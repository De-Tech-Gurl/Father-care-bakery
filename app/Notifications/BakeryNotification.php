<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BakeryNotification extends Notification implements ShouldQueue
{
    use Queueable;

    private bool $mailOnly = false;

    /**
     * @param  array<string, mixed>  $context
     */
    public function __construct(
        public string $type,
        public string $title,
        public string $message,
        public ?string $url = null,
        public string $icon = 'ph-bell',
        public array $context = [],
    ) {}

    public function via(User $notifiable): array
    {
        $channels = $notifiable->notificationChannelsFor($this->type);

        return $this->mailOnly
            ? array_values(array_intersect($channels, ['mail']))
            : $channels;
    }

    public function mailOnly(): static
    {
        $notification = clone $this;
        $notification->mailOnly = true;

        return $notification;
    }

    public function toMail(User $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject($this->title)
            ->greeting('Hello '.$notifiable->name)
            ->line($this->message);

        if ($this->url !== null) {
            $mail->action('View details', $this->url);
        }

        return $mail->line('Father Care Bakery');
    }

    public function toDatabase(User $notifiable): array
    {
        return [
            'type' => $this->type,
            'title' => $this->title,
            'message' => $this->message,
            'url' => $this->url,
            'icon' => $this->icon,
            'context' => $this->context,
        ];
    }

    public function toArray(User $notifiable): array
    {
        return $this->toDatabase($notifiable);
    }
}
