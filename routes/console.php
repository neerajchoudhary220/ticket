 <?php

use App\Models\Draw;
use App\Models\DrawDetail;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;
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
    DB::table('cross_abc_details')->truncate();
    DB::table('cross_abcs')->truncate();
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

Artisan::command('neeraj', function () {
    $my_collection = collect([
        [
            'ab' => [23, 12, 78, 9],
            'ac' => [13, 22],
            'bc' => [],
            'amt' => 10,
            'combination' => 5,
        ],
        [
            'ab' => [23, 12, 78, 9],
            'ac' => [13, 22, 90],
            'bc' => [13, 22, 90],
            'amt' => 15,
            'combination' => 10,

        ],
        [
            'ab' => [12],
            'ac' => [],
            'bc' => [13],
            'amt' => 15,
            'combination' => 5,

        ],
    ]);

    $output = collect(['ab', 'ac', 'bc'])->mapWithKeys(function ($key) use ($my_collection) {
        $items = $my_collection->flatMap(function ($row) use ($key) {
            return collect($row[$key])->map(function ($number) use ($row) {
                return [
                    'number' => $number,
                    'amt' => $row['amt'],
                    'combination' => $row['combination'],
                ];
            });
        })->values();

        return [$key => $items];
    })->toArray();
    $this->info(print_r($output, true));
});
