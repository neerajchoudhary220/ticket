<?php

namespace App\Livewire;

use App\Models\Draw;
use App\Models\TicketOption;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Livewire\Attributes\On;
use Livewire\Component;

class AddTicketForm extends Component
{
    public $auth_user;

    public $active_draw_number;

    public $a;

    public $b;

    public $c;

    public $a_qty = 0;

    public $b_qty = 0;

    public $c_qty = 0;

    public $total_a = 0;

    public $total_b = 0;

    public $total_c = 0;

    const PRICE = 11;

    public $end_time;

    public int $duration = 0;

    public function boot(Request $request)
    {
        $this->auth_user = $request->user();
        $this->intialdata();
    }
    // public function mount(Request $request)
    // {
    //     $this->auth_user = $request->user();
    //     $this->intialdata();
    // }

    protected function intialdata()
    {
        $ticketNumber = 102;
        $activeDraw = Draw::runningDraw()->first();
        $this->active_draw_number = $activeDraw->draw_number;
        $endTime = Carbon::createFromFormat('H:i', $activeDraw->end_time);
        $current_time = Carbon::now()->setTimezone('Asia/Kolkata')->format('h:i A');
        $this->end_time = $endTime->format('h:i A');
        $this->duration = $endTime->diffInMinutes($current_time, true);

        if ($activeDraw) {
            $options = [];

            for ($i = 0; $i < 10; $i++) {
                $options[] = [
                    'draw_id' => $activeDraw->id,
                    'user_id' => $this->auth_user->id,
                    'ticket_number' => $ticketNumber,
                    'number' => $i,
                ];
            }

            foreach ($options as $option) {
                TicketOption::firstOrCreate([
                    'draw_id' => $option['draw_id'],
                    'user_id' => $option['user_id'],
                    'ticket_number' => $option['ticket_number'],
                    'number' => $option['number'],
                ]);
            }
        }

    }

    public function move($focus, $row_property)
    {
        $this->calculateTotal($row_property);
        $this->dispatch($focus);

    }

    public function keyTab($row_property)
    {
        $this->calculateTotal($row_property);
    }

    public function calculateTotal($row_property)
    {
        $this->{'total_'.$row_property} = self::PRICE * $this->{$row_property.'_qty'};
    }

    #[On('timer-finished')]
    public function handleTimerFinished()
    {
        logger()->info('Timer finished!');
        // You can redirect, emit events, update data, etc.
        // return redirect()->route('some.route');
    }

    public function render()
    {
        return view('livewire.add-ticket-form');
    }
}
