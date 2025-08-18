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
    function generateCombinations($a, $b, $c)
    {
        // Convert each into array of digits
        $aDigits = str_split((string) $a);
        $bDigits = str_split((string) $b);
        $cDigits = str_split((string) $c);

        // Helper closure to generate combinations
        $makePairs = function ($x, $y) {
            $pairs = [];
            foreach ($x as $dx) {
                foreach ($y as $dy) {
                    $pairs[] = (int) ($dx.$dy);
                }
            }

            return $pairs;
        };

        // ✅ Only forward direction, not both
        $ab = $makePairs($aDigits, $bDigits);
        $ac = $makePairs($aDigits, $cDigits);
        $bc = $makePairs($bDigits, $cDigits);

        // Total count
        $total = count($ab) + count($ac) + count($bc);

        return [
            'ab' => $ab,
            'ac' => $ac,
            'bc' => $bc,
            'total' => $total,
        ];
    }

    $a = 18;
    $b = 43;
    $c = 74;
    $output = generateCombinations($a, $b, $c);
    logger()->info($output);
});
