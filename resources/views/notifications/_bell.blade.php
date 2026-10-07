<div class="dropdown notification-bell" data-notification-menu>
    <button class="notification-bell__trigger" type="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Notifications" title="Notifications">
        <i class="ph ph-bell" aria-hidden="true"></i>
        <span class="notification-bell__count" data-notification-badge @if(($unreadNotificationCount ?? 0) < 1) hidden @endif>
            {{ min($unreadNotificationCount ?? 0, 99) }}{{ ($unreadNotificationCount ?? 0) > 99 ? '+' : '' }}
        </span>
    </button>
    <div class="dropdown-menu dropdown-menu-end notification-menu-panel mt-2">
        <div class="notification-menu-heading">
            <div class="notification-menu-heading-copy">
                <strong>Notifications</strong>
                <span data-notification-unread-label>{{ ($unreadNotificationCount ?? 0).' unread' }}</span>
            </div>
            <a href="{{ route('notifications.index') }}">View all</a>
        </div>
        <ul class="notification-menu-list" data-notification-items>
            @forelse($headerNotifications ?? collect() as $notification)
                <li>
                    <a class="notification-menu-item {{ $notification->read_at ? '' : 'is-unread' }}" href="{{ route('notifications.open', $notification) }}">
                        <i class="notification-menu-icon ph {{ $notification->data['icon'] ?? 'ph-bell' }}" aria-hidden="true"></i>
                        <span class="notification-menu-copy">
                            <strong>{{ $notification->data['title'] ?? 'Notification' }}</strong>
                            <span>{{ $notification->data['message'] ?? '' }}</span>
                            <time>{{ $notification->created_at?->timezone(config('app.timezone'))->diffForHumans() }}</time>
                        </span>
                    </a>
                </li>
            @empty
                <li class="notification-menu-empty">No notifications yet.</li>
            @endforelse
        </ul>
        <div class="notification-menu-footer"><a href="{{ route('notifications.index') }}">Open notification center</a></div>
    </div>
</div>