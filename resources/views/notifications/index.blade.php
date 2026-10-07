@extends(auth()->user()->isAdmin() || auth()->user()->isStaff() ? 'layouts.admin' : 'layouts.customer')

@section('title', 'Notifications — Father Care Bakery')

@section('content')
    @php
        $savedPreferences = auth()->user()->notification_preferences ?? [];
    @endphp

    <section class="notification-page">
        <div class="notification-page__heading">
            <div>
                <h1>Notifications</h1>
                <p>{{ $notifications->total() }} total</p>
            </div>
            <div class="notification-page__actions">
                @if(auth()->user()->unreadNotifications()->exists())
                    <form method="POST" action="{{ route('notifications.read-all') }}">
                        @csrf
                        @method('PATCH')
                        <button class="notification-page__button" type="submit"><i class="ph ph-checks"></i> Mark all read</button>
                    </form>
                @endif
                <a class="notification-page__button" href="#notification-preferences"><i class="ph ph-sliders-horizontal"></i> Preferences</a>
            </div>
        </div>

        <form class="notification-filterbar" method="GET" action="{{ route('notifications.index') }}">
            <select name="filter" aria-label="Read status" onchange="this.form.submit()">
                <option value="all" @selected($activeFilter !== 'unread')>All notifications</option>
                <option value="unread" @selected($activeFilter === 'unread')>Unread only</option>
            </select>
            <select name="type" aria-label="Notification type" onchange="this.form.submit()">
                <option value="">All types</option>
                @foreach($types as $type => $definition)
                    <option value="{{ $type }}" @selected($activeType === $type)>{{ $definition['label'] }}</option>
                @endforeach
            </select>
        </form>

        <div class="notification-feed">
            @forelse($notifications as $notification)
                @php($type = $notification->data['type'] ?? '')
                <article class="notification-feed-row {{ $notification->read_at ? '' : 'is-unread' }}">
                    <i class="notification-feed-row__icon ph {{ $notification->data['icon'] ?? 'ph-bell' }}" aria-hidden="true"></i>
                    <div class="notification-feed-row__body">
                        <a class="notification-feed-row__title" href="{{ route('notifications.open', $notification) }}">
                            {{ $notification->data['title'] ?? 'Notification' }}
                        </a>
                        <p class="notification-feed-row__message">{{ $notification->data['message'] ?? '' }}</p>
                        <div class="notification-feed-row__meta">
                            <span class="notification-feed-row__type">{{ $types[$type]['label'] ?? 'Update' }}</span>
                            <time title="{{ $notification->created_at?->timezone(config('app.timezone'))->format('M j, Y g:i A T') }}">
                                {{ $notification->created_at?->timezone(config('app.timezone'))->diffForHumans() }}
                            </time>
                        </div>
                    </div>
                    <div class="notification-feed-row__controls">
                        @if(! $notification->read_at)
                            <form method="POST" action="{{ route('notifications.read', $notification) }}">
                                @csrf
                                @method('PATCH')
                                <button class="notification-icon-button" type="submit" title="Mark as read" aria-label="Mark as read">
                                    <i class="ph ph-check"></i>
                                </button>
                            </form>
                        @endif
                        <form method="POST" action="{{ route('notifications.destroy', $notification) }}">
                            @csrf
                            @method('DELETE')
                            <button class="notification-icon-button" type="submit" title="Delete notification" aria-label="Delete notification">
                                <i class="ph ph-trash"></i>
                            </button>
                        </form>
                    </div>
                </article>
            @empty
                <div class="notification-empty-state">
                    <i class="ph ph-bell-slash" aria-hidden="true"></i>
                    <strong>No notifications match these filters.</strong>
                </div>
            @endforelse

            @if($notifications->hasPages())
                <div class="notification-pagination">{{ $notifications->links() }}</div>
            @endif
        </div>

        <details class="notification-preferences" id="notification-preferences">
            <summary>Notification preferences</summary>
            <div class="notification-preferences__content">
                <p class="notification-preferences__hint">Choose in-app and email delivery separately for each update.</p>
                <form method="POST" action="{{ route('notifications.preferences.update') }}">
                    @csrf
                    @method('PUT')
                    <div class="notification-preferences__row notification-preferences__head" aria-hidden="true">
                        <span>Notification type</span><span class="notification-preferences__toggle">In-app</span><span class="notification-preferences__toggle">Email</span>
                    </div>
                    @foreach($types as $type => $definition)
                        @php($channels = $savedPreferences[$type] ?? ['database' => true, 'mail' => ($definition['mail_default'] ?? false)])
                        <div class="notification-preferences__row">
                            <strong>{{ $definition['label'] }}</strong>
                            @foreach(['database', 'mail'] as $channel)
                                <label class="notification-preferences__toggle">
                                    <input type="hidden" name="preferences[{{ $type }}][{{ $channel }}]" value="0">
                                    <input type="checkbox" name="preferences[{{ $type }}][{{ $channel }}]" value="1"
                                           @checked((bool) ($channels[$channel] ?? false))
                                           aria-label="{{ $definition['label'] }} {{ $channel === 'database' ? 'in-app' : 'email' }}">
                                </label>
                            @endforeach
                        </div>
                    @endforeach
                    <div style="padding-top:14px;">
                        <button class="notification-page__button notification-page__button--primary" type="submit"><i class="ph ph-floppy-disk"></i> Save preferences</button>
                    </div>
                </form>
            </div>
        </details>
    </section>
@endsection