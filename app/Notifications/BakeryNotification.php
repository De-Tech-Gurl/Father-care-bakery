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
        return $notifiable->notificationChannelsFor($this->type);
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
