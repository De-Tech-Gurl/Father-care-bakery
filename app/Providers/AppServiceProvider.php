<?php

namespace App\Providers;

use App\Contracts\Repositories\ProductRepositoryInterface;
use App\Models\Product;
use App\Models\User;
use App\Observers\ProductObserver;
use App\Observers\UserObserver;
use App\Repositories\ProductRepository;
use App\Services\CartService;
use Illuminate\Console\Events\ScheduledTaskFailed;
use Illuminate\Pagination\Paginator;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(ProductRepositoryInterface::class, ProductRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();

        Product::observe(ProductObserver::class);
        User::observe(UserObserver::class);

        View::composer('layouts.customer', function ($view) {
            $view->with('cartCount', app(CartService::class)->count());
        });

        View::composer(['layouts.admin', 'layouts.customer'], function ($view): void {
            $user = auth()->user();

            $view->with('unreadNotificationCount', $user?->unreadNotifications()->count() ?? 0);
            $view->with('headerNotifications', $user?->notifications()
                ->orderByDesc('created_at')
                ->orderByDesc('id')
                ->limit(8)
                ->get() ?? collect());
        });

        Event::listen(JobFailed::class, function (JobFailed $event): void {
            $jobName = method_exists($event->job, 'resolveName')
                ? $event->job->resolveName()
                : class_basename($event->job);

            if (str_contains($jobName, 'SendQueuedNotifications')) {
                Log::error('Queued notification delivery failed.', [
                    'connection' => $event->connectionName,
                    'queue' => $event->job->getQueue(),
                    'exception' => $event->exception,
                ]);

                return;
            }

            event(new NotificationRequested(
                'system.queue_job_failed',
                'Queue job failed',
                'A queued job failed on the '.$event->connectionName.' connection. Check the application logs.',
                'staff',
                url: route('admin.dashboard'),
                icon: 'ph-warning-circle',
                context: ['job' => $jobName],
            ));
        });

        Event::listen(ScheduledTaskFailed::class, function (ScheduledTaskFailed $event): void {
            event(new NotificationRequested(
                'system.scheduled_task_failed',
                'Scheduled task failed',
                'A scheduled task failed. Check the application logs for details.',
                'staff',
                url: route('admin.dashboard'),
                icon: 'ph-warning-circle',
                context: ['task' => $event->task->getSummaryForDisplay()],
            ));
        });
    }
}
