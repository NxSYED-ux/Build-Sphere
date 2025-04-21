<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->everySecond();

Artisan::command('start:queue', function () {
    $this->info('Queue worker started...');
    Artisan::call('queue:work', [
        '--tries' => 3,
        '--stop-when-empty' => true,
    ]);
})->describe('Start the Laravel queue worker');

Artisan::command('version', function () {
    $version = DB::selectOne('SELECT version() as version')->version;
    $this->info("Database Version: " . $version);
});
