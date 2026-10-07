<?php

namespace App\Events;

use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;
use Illuminate\Foundation\Events\Dispatchable;

class NotificationRequested implements ShouldDispatchAfterCommit
{
    use Dispatchable;

    /**
     * @param  array<string, mixed>  $context
     */
    public function __construct(
        public string $type,
        public string $title,
        public string $message,
        public string $audience,
        public ?int $recipientId = null,
        public ?string $url = null,
        public string $icon = 'ph-bell',
        public array $context = [],
    ) {}
}
