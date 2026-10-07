<?php

return [
    'types' => [
        'order.placed' => ['label' => 'New order placed', 'mail_default' => true],
        'order.status_changed' => ['label' => 'Order status changed', 'mail_default' => true],
        'payment.received' => ['label' => 'Payment received', 'mail_default' => true],
        'payment.failed' => ['label' => 'Payment failed', 'mail_default' => true],
        'payment.status_changed' => ['label' => 'Payment status changed', 'mail_default' => true],
        'inventory.low_stock' => ['label' => 'Low stock'],
        'inventory.out_of_stock' => ['label' => 'Out of stock'],
        'inventory.restocked' => ['label' => 'Product restocked'],
        'product.created' => ['label' => 'Product added'],
        'product.price_changed' => ['label' => 'Product price changed'],
        'product.deactivated' => ['label' => 'Product deactivated'],
        'report.daily_summary' => ['label' => 'Daily sales summary'],
        'report.weekly_ready' => ['label' => 'Weekly report ready'],
        'user.registered' => ['label' => 'New user registered'],
        'user.role_changed' => ['label' => 'User role changed'],
        'user.password_changed' => ['label' => 'Password changed'],
        'security.failed_login' => ['label' => 'Failed login attempt'],
        'system.scheduled_task_failed' => ['label' => 'Scheduled task failed'],
        'system.queue_job_failed' => ['label' => 'Queue job failed'],
        'system.backup_result' => ['label' => 'Backup result'],
    ],
];
