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

Artisan::command('add-details', function () {
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
    $this->info('working...');
});
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
    $t1 = Carbon::createFromTimeString('16:48')->format('H:i');
    $t2 = Carbon::now()->setSecond(0)->timezone('Asia/Kolkata'); // keep as Carbon object, remove seconds if you want exact minute comparison
    $this->info($t1);
    $this->info($t2->format('H:i')); // only format when displaying

    if ($t2->gte($t1)) {
        $this->info('yes');
    }
});

Artisan::command('getData', function () {
    $page = $this->ask('Enter the page number:');
    $tickets = cache::get('tickets');
    $outputs = getScrollPage($tickets, 10, (int) $page);
    dd($outputs);

});
