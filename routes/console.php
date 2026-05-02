<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule cache cleanup setiap hari jam 2 pagi
Schedule::command('cache:clear-expired')->daily()->at('02:00');

// Optional: cleanup setiap 6 jam untuk cache yang lebih agresif
// Schedule::command('cache:clear-expired')->everySixHours();
