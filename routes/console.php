<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('notifications:check-low-stock')
    ->everyFifteenMinutes()
    ->withoutOverlapping();

Schedule::command('reports:notify-daily')
    ->dailyAt('07:00')
    ->withoutOverlapping();

Schedule::command('reports:notify-weekly')
    ->weeklyOn(1, '07:15')
    ->withoutOverlapping();
