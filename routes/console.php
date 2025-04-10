<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    while (true) {
        $this->comment(Inspiring::quote());
        sleep(5);
    }
})->purpose('Display an inspiring quote')->everySecond();
