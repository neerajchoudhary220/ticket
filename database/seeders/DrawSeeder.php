<?php

namespace Database\Seeders;

use App\Models\Draw;
use Illuminate\Database\Seeder;

class DrawSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [

            ['price' => 11, 'start_time' => '08:30', 'end_time' => '08:45'],
            ['price' => 11, 'start_time' => '08:45', 'end_time' => '09:00'],
            ['price' => 11, 'start_time' => '09:00', 'end_time' => '09:15'],
            ['price' => 11, 'start_time' => '09:15', 'end_time' => '09:30'],
            ['price' => 11, 'start_time' => '09:30', 'end_time' => '09:45'],
            ['price' => 11, 'start_time' => '09:45', 'end_time' => '10:00'],
            ['price' => 11, 'start_time' => '10:00', 'end_time' => '10:15'],
            ['price' => 11, 'start_time' => '10:15', 'end_time' => '10:30'],
            ['price' => 11, 'start_time' => '10:30', 'end_time' => '10:45'],
            ['price' => 11, 'start_time' => '10:45', 'end_time' => '11:00'],
            ['price' => 11, 'start_time' => '11:00', 'end_time' => '11:15'],
            ['price' => 11, 'start_time' => '11:15', 'end_time' => '11:30'],
            ['price' => 11, 'start_time' => '11:30', 'end_time' => '11:45'],
            ['price' => 11, 'start_time' => '11:45', 'end_time' => '12:00'],
            ['price' => 11, 'start_time' => '12:00', 'end_time' => '12:15'],
            ['price' => 11, 'start_time' => '12:15', 'end_time' => '12:30'],
            ['price' => 11, 'start_time' => '12:30', 'end_time' => '12:45'],
            ['price' => 11, 'start_time' => '12:45', 'end_time' => '13:00'],
            ['price' => 11, 'start_time' => '13:00', 'end_time' => '13:15'],
            ['price' => 11, 'start_time' => '13:15', 'end_time' => '13:30'],
            ['price' => 11, 'start_time' => '13:30', 'end_time' => '13:45'],
            ['price' => 11, 'start_time' => '13:45', 'end_time' => '14:00'],
            ['price' => 11, 'start_time' => '14:00', 'end_time' => '14:15'],
            ['price' => 11, 'start_time' => '14:15', 'end_time' => '14:30'],
            ['price' => 11, 'start_time' => '14:30', 'end_time' => '14:45'],
            ['price' => 11, 'start_time' => '14:45', 'end_time' => '15:00'],
            ['price' => 11, 'start_time' => '15:00', 'end_time' => '15:15'],
            ['price' => 11, 'start_time' => '15:15', 'end_time' => '15:30'],
            ['price' => 11, 'start_time' => '15:30', 'end_time' => '15:45'],
            ['price' => 11, 'start_time' => '15:45', 'end_time' => '16:00'],
            ['price' => 11, 'start_time' => '16:00', 'end_time' => '16:15'],
            ['price' => 11, 'start_time' => '16:15', 'end_time' => '16:30'],
            ['price' => 11, 'start_time' => '16:30', 'end_time' => '16:45'],
            ['price' => 11, 'start_time' => '16:45', 'end_time' => '17:00'],
            ['price' => 11, 'start_time' => '17:00', 'end_time' => '17:15'],
            ['price' => 11, 'start_time' => '17:15', 'end_time' => '17:30'],
            ['price' => 11, 'start_time' => '17:30', 'end_time' => '17:45'],
            ['price' => 11, 'start_time' => '17:45', 'end_time' => '18:00'],
            ['price' => 11, 'start_time' => '18:00', 'end_time' => '18:15'],
            ['price' => 11, 'start_time' => '18:15', 'end_time' => '18:30'],
            ['price' => 11, 'start_time' => '18:30', 'end_time' => '18:45'],
            ['price' => 11, 'start_time' => '18:45', 'end_time' => '19:00'],
            ['price' => 11, 'start_time' => '19:00', 'end_time' => '19:15'],
            ['price' => 11, 'start_time' => '19:15', 'end_time' => '19:30'],
            ['price' => 11, 'start_time' => '19:30', 'end_time' => '19:45'],
            ['price' => 11, 'start_time' => '19:45', 'end_time' => '20:00'],
            ['price' => 11, 'start_time' => '20:00', 'end_time' => '20:15'],
            ['price' => 11, 'start_time' => '20:15', 'end_time' => '20:30'],
            ['price' => 11, 'start_time' => '20:30', 'end_time' => '20:45'],
            ['price' => 11, 'start_time' => '20:45', 'end_time' => '21:00'],
            ['price' => 11, 'start_time' => '21:00', 'end_time' => '21:15'],
            ['price' => 11, 'start_time' => '21:15', 'end_time' => '21:30'],
            ['price' => 11, 'start_time' => '21:30', 'end_time' => '21:45'],
            ['price' => 11, 'start_time' => '21:45', 'end_time' => '22:00'],

        ];

        foreach ($data as $time) {
            Draw::updateOrCreate(['start_time' => $time['start_time'], 'end_time' => $time['end_time']],
                ['price' => $time['price']]);
        }

    }
}
