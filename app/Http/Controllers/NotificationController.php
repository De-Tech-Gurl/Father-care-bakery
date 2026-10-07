<?php

namespace App\Http\Controllers;

use App\Http\Requests\NotificationIndexRequest;
use App\Http\Requests\SaveNotificationPreferencesRequest;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\UserNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(NotificationIndexRequest $request): View
    {
        $query = $request->user()->notifications()
            ->orderByDesc('created_at')
            ->orderByDesc('id');

        if ($request->validated('filter') === 'unread') {
            $query->unread();
        }

        if ($request->filled('type')) {
            $query->where('data->type', $request->validated('type'));
        }

        return view('notifications.index', [
            'notifications' => $query->paginate(20)->withQueryString(),
            'types' => config('notifications.types'),
            'activeFilter' => $request->validated('filter', 'all'),
            'activeType' => $request->validated('type'),
        ]);
    }

    public function unreadCount(): JsonResponse
    {
        $user = request()->user();

        return response()->json([
            'unread_count' => $user->unreadNotifications()->count(),
            'notifications' => $user->notifications()
                ->orderByDesc('created_at')
                ->orderByDesc('id')
                ->limit(8)
                ->get()
                ->map(fn (UserNotification $notification): array => [
                    'id' => $notification->id,
                    'title' => data_get($notification->data, 'title', 'Notification'),
                    'message' => data_get($notification->data, 'message', ''),
                    'icon' => data_get($notification->data, 'icon', 'ph-bell'),
                    'read' => $notification->read_at !== null,
                    'time' => $notification->created_at?->timezone(config('app.timezone'))->diffForHumans(),
                    'url' => route('notifications.open', $notification),
                ]),
        ]);
    }

    public function open(UserNotification $notification): RedirectResponse
    {
        Gate::authorize('view', $notification);
        $notification->markAsRead();

        return redirect()->to($this->destination(request()->user(), $notification));
    }

    public function markAsRead(UserNotification $notification): RedirectResponse
    {
        Gate::authorize('view', $notification);
        $notification->markAsRead();

        return back()->with('success', 'Notification marked as read.');
    }

    public function markAllAsRead(): RedirectResponse
    {
        request()->user()->unreadNotifications()->update(['read_at' => now()]);

        return back()->with('success', 'All notifications marked as read.');
    }

    public function destroy(UserNotification $notification): RedirectResponse
    {
        Gate::authorize('delete', $notification);
        $notification->delete();

        return back()->with('success', 'Notification deleted.');
    }

    public function updatePreferences(SaveNotificationPreferencesRequest $request): RedirectResponse
    {
        $submitted = $request->validated('preferences');
        $existingPreferences = $request->user()->notification_preferences ?? [];
        $preferences = [];

        foreach (config('notifications.types', []) as $type => $definition) {
            $submittedType = $submitted[$type] ?? [];
            $existingType = $existingPreferences[$type] ?? [];

            $preferences[$type] = [
                'database' => (bool) ($submittedType['database'] ?? $existingType['database'] ?? true),
                'mail' => (bool) ($submittedType['mail'] ?? $existingType['mail'] ?? ($definition['mail_default'] ?? false)),
            ];
        }

        $request->user()->forceFill(['notification_preferences' => $preferences])->save();

        return redirect()->route('notifications.index')->with('success', 'Notification preferences saved.');
    }

    private function destination(User $user, UserNotification $notification): string
    {
        $context = $notification->data['context'] ?? [];

        if (isset($context['product_id'])) {
            $product = Product::query()->find($context['product_id']);

            if ($product && ($user->isAdmin() || $user->isStaff())) {
                return route('admin.products.edit', $product);
            }

            return route('notifications.index');
        }

        if (isset($context['order_id'])) {
            $order = Order::query()->find($context['order_id']);

            if (! $order || ($user->isCustomer() && $order->user_id !== $user->id)) {
                return route('notifications.index');
            }

            return $user->isAdmin() || $user->isStaff()
                ? route('admin.orders.show', $order)
                : route('customer.orders.show', $order);
        }

        $url = $notification->data['url'] ?? null;

        if (! is_string($url) || $url === '' || str_starts_with($url, '//')) {
            return route('notifications.index');
        }

        $host = parse_url($url, PHP_URL_HOST);

        if ($host !== null && strcasecmp($host, request()->getHost()) !== 0) {
            return route('notifications.index');
        }

        return $url;
    }
}
