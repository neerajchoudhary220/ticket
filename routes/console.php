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

Artisan::command('neeraj', function () {
    $arr = [
        [
            'id' => 1,
            'qty' => 20,
            'ticket_id' => 1,
        ],
        [
            'id' => 2,
            'qty' => 25,
            'ticket_id' => 1,
        ],
    ];

    $selected_draw_ids = [2, 3, 5];
    $new_options = [];
    foreach ($selected_draw_ids as $draw_id) {
        $new_option = collect($arr)->map(function ($option) use ($draw_id) {
            $option['draw_id'] = $draw_id;

            return $option;
        })->values()->all();
        $new_options = array_merge($new_options, $new_option);
    }
    logger()->info($new_options);
    unset($new_options[1]);
    logger()->info($new_options);

});
