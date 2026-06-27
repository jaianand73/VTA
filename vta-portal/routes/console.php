<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('inspire')->everyMinute();

Schedule::call(function () {
    app(\App\Services\EmailIntakeService::class)->fetch();
})->name('email-intake')->everyFifteenMinutes()->withoutOverlapping();
