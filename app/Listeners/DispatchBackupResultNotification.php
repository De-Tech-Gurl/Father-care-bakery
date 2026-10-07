<?php

namespace App\Listeners;

use App\Events\BackupResultReported;
use App\Events\NotificationRequested;

class DispatchBackupResultNotification
{
    public function handle(BackupResultReported $event): void
    {
        event(new NotificationRequested(
            'system.backup_result',
            $event->succeeded ? 'Backup completed' : 'Backup failed',
            $event->summary,
            'staff',
            url: route('admin.dashboard'),
            icon: $event->succeeded ? 'ph-hard-drives' : 'ph-warning-circle',
        ));
    }
}
