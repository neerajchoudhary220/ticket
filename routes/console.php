 <?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
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

function getScrollPage($items, $perPage = 10, $page = 1)
{
    $items = $items instanceof Collection ? $items : collect($items);

    return $items->slice(($page - 1) * $perPage, $perPage)->values();
}

Artisan::command('neeraj', function () {
    $collection = collect(range(1, 50))->map(function ($id) {
        return [
            'id' => $id,
            'qty' => rand(1, 500), // random qty between 1 and 500
            'ticket_id' => 1,
        ];
    });
    $collection->forget(2);
    dd($collection->values()->all());
    // cache::forget('tickets');
    // Cache::put('tickets', $collection);
});

Artisan::command('getData', function () {
    $page = $this->ask('Enter the page number:');
    $tickets = cache::get('tickets');
    $outputs = getScrollPage($tickets, 10, (int) $page);
    dd($outputs);

});
