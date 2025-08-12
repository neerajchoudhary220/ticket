 <?php

use App\Models\Draw;
use App\Models\DrawDetail;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schedule;

Schedule::call(function () {
    $draws = Draw::get();
    foreach ($draws as $draw) {
        DrawDetail::updateOrCreate([
            'date' => Carbon::today(),
            'draw_id' => $draw->id,
        ],
            [
                'draw_id' => $draw->id,
                'start_time' => $draw->start_time,
                'end_time' => $draw->end_time,
            ]
        );
    }
})->dailyAt('00:05');

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('test', function () {
    DB::statement('SET FOREIGN_KEY_CHECKS=0;');
    DB::table('ticket_options')->truncate();
    DB::table('tickets')->truncate();
    DB::table('options')->truncate();
    DB::table('user_draws')->truncate();

    DB::table('draw_details')->truncate();
    $draws = Draw::get();
    foreach ($draws as $draw) {
        DrawDetail::updateOrCreate([
            'date' => Carbon::today(),
            'draw_id' => $draw->id,
        ],
            [
                'draw_id' => $draw->id,
                'start_time' => $draw->start_time,
                'end_time' => $draw->end_time,
            ]
        );
    }

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
