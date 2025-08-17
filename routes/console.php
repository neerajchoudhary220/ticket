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
    function makeCombination($cross_abc, $input_combination): array
    {
        $chars = str_split($cross_abc);
        $keys = ['ab', 'ac', 'bc'];
        $result = [];

        if ($input_combination > 9) {
            // Situation 1: generate full combinations with repetition
            foreach ($keys as $key) {
                $combis = [];
                foreach ($chars as $first) {
                    foreach ($chars as $second) {
                        $combis[] = (int) ($first.$second);
                    }
                }
                $result[$key] = $combis;
            }
        }
        //  else {
        //     // Situation 2: take only unique direct pairs
        //     $pairs = [
        //         'ab' => (int) ($chars[0].$chars[1]),
        //         'ac' => (int) ($chars[0].$chars[2]),
        //         'bc' => (int) ($chars[1].$chars[2]),
        //     ];
        //     foreach ($pairs as $key => $value) {
        //         $result[$key] = [$value];
        //     }
        // }

        return $result;
    }

    $cross_abc = 186;
    $input_combination = 27;
    $output = makeCombination($cross_abc, $input_combination);
    logger()->info($output);

});
