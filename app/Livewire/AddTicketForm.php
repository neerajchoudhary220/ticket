<?php

namespace App\Livewire;

use App\Models\Draw;
use App\Models\Options;
use App\Models\Ticket;
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

    public $a_qty = '';

    public $b_qty = '';

    public $c_qty = '';

    public $total_a = 0;

    public $total_b = 0;

    public $total_c = 0;

    const PRICE = 11;

    public $end_time;

    public int $duration = 0;

    public $active_draw;

    public $ticket;

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
        $ticketNumber = '123';
        $this->active_draw = Draw::runningDraw()->first();
        $this->active_draw_number = $this->active_draw->draw_number;
        $endTime = Carbon::createFromFormat('H:i', $this->active_draw->end_time);
        $current_time = Carbon::now()->setTimezone('Asia/Kolkata')->format('h:i A');
        $this->end_time = $endTime->format('h:i A');
        $this->duration = $endTime->diffInMinutes($current_time, true);

        if ($this->active_draw) {
            $this->ticket = Ticket::firstOrCreate([
                'user_id' => $this->auth_user->id,
                'ticket_number' => $ticketNumber,
            ]);

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
        if ($this->{$row_property.'_qty'}) {
            $this->{'total_'.$row_property} = self::PRICE * $this->{$row_property.'_qty'};

            return $this->{'total_'.$row_property};
        }
    }

    public function keyEnter($row_property)
    {
        $total = $this->calculateTotal($row_property);
        if ($this->active_draw) {
            $option = Options::create([
                'user_id' => $this->auth_user->id,
                'draw_id' => $this->active_draw->id,
                'ticket_id' => $this->ticket->id,
                'number' => $this->{$row_property},
                'option' => ucfirst($row_property),
                'qty' => $this->{$row_property.'_qty'},
                'total' => $total,
            ]);

        }

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
