<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('test', function () {
    DB::statement('SET FOREIGN_KEY_CHECKS=0;');
    DB::table('ticket_options')->truncate();
    DB::table('tickets')->truncate();
    DB::table('options')->truncate();
    DB::table('user_draws')->truncate();

    DB::statement('SET FOREIGN_KEY_CHECKS=1;');

    $this->info('done!');

});
