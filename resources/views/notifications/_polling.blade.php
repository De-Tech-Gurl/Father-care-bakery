@auth
    <script>
        (() => {
            const menus = document.querySelectorAll('[data-notification-menu]');
            if (!menus.length) return;

            const endpoint = @json(route('notifications.unread-count'));

            const refreshNotifications = async () => {
                try {
                    const response = await fetch(endpoint, {
                        headers: { Accept: 'application/json' },
                        credentials: 'same-origin',
                    });

                    if (!response.ok) return;

                    const payload = await response.json();
                    const count = Number(payload.unread_count) || 0;

                    menus.forEach((menu) => {
                        const badge = menu.querySelector('[data-notification-badge]');
                        const label = menu.querySelector('[data-notification-unread-label]');
                        const list = menu.querySelector('[data-notification-items]');

                        if (badge) {
                            badge.textContent = count > 99 ? '99+' : String(count);
                            badge.hidden = count === 0;
                        }

                        if (label) label.textContent = `${count} unread`;
                        if (!list) return;

                        list.replaceChildren();

                        if (!payload.notifications?.length) {
                            const empty = document.createElement('li');
                            empty.className = 'notification-menu-empty';
                            empty.textContent = 'No notifications yet.';
                            list.append(empty);
                            return;
                        }

                        payload.notifications.forEach((notification) => {
                            const row = document.createElement('li');
                            const link = document.createElement('a');
                            const icon = document.createElement('i');
                            const copy = document.createElement('span');
                            const title = document.createElement('strong');
                            const message = document.createElement('span');
                            const time = document.createElement('time');

                            link.className = `notification-menu-item${notification.read ? '' : ' is-unread'}`;
                            link.href = notification.url;
                            icon.className = `notification-menu-icon ph ${/^ph-[a-z0-9-]+$/.test(notification.icon) ? notification.icon : 'ph-bell'}`;
                            icon.setAttribute('aria-hidden', 'true');
                            copy.className = 'notification-menu-copy';
                            title.textContent = notification.title;
                            message.textContent = notification.message;
                            time.textContent = notification.time ?? '';
                            copy.append(title, message, time);
                            link.append(icon, copy);
                            row.append(link);
                            list.append(row);
                        });
                    });
                } catch (error) {
                    console.error('Unable to refresh notifications.', error);
                }
            };

            window.setInterval(refreshNotifications, 45000);

            document.querySelectorAll('.notification-action-toast').forEach((toast) => {
                window.setTimeout(() => {
                    toast.classList.add('is-dismissing');
                    window.setTimeout(() => toast.remove(), 300);
                }, 5000);
            });
        })();
    </script>
@endauth