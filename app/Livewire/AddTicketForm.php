<?php

namespace App\Livewire;

use App\Models\Draw;
use App\Models\Options;
use App\Models\Ticket;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class AddTicketForm extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap'; // For Bootstrap 5

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

    public $search = '';

    public $filterOption = '';

    // protected $updatesQueryString = ['search', 'filterOption'];

    public function mount(Request $request)
    {
        $this->auth_user = User::find($request->user()->id);

        $this->intialdata();
    }

    // public function updatingSearch()
    // {
    //     $this->resetPage();
    // }

    // public function updatingFilterOption()
    // {
    //     $this->resetPage();
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
            $this->auth_user->draws()->syncWithoutDetaching($this->active_draw->id);
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
            $this->{'total_'.$row_property} = self::PRICE * ($this->{$row_property.'_qty'} * str()->length($this->{$row_property}));

            return $this->{'total_'.$row_property};
        }
    }

    public function keyEnter($row_property, $focus)
    {
        $total = $this->calculateTotal($row_property);
        if ($this->active_draw) {
            Options::create([
                'user_id' => $this->auth_user->id,
                'draw_id' => $this->active_draw->id,
                'ticket_id' => $this->ticket->id,
                'number' => $this->{$row_property},
                'option' => ucfirst($row_property),
                'qty' => $this->{$row_property.'_qty'},
                'total' => $total,
                'status' => false,
            ]);
            $this->dispatch($focus);
            $this->{$row_property} = '';
            $this->{$row_property.'_qty'} = '';
            $this->{'total_'.$row_property} = 0;

        }

    }

    #[On('timer-finished')]
    public function handleTimerFinished()
    {
        // logger()->info('Timer finished!');
        // You can redirect, emit events, update data, etc.
        // return redirect()->route('some.route');
    }

    public function deleteOption(Options $option)
    {
        $option->delete();
    }

    public function render()
    {
        // $query = Options::query()
        //     ->where('draw_id', $this->active_draw->id)
        //     ->where('ticket_id', $this->ticket->id)
        //     ->where('user_id', $this->auth_user->id);

        // if ($this->search) {
        //     dd('working');
        //     $query->where(function ($q) {
        //         $q->where('option', 'like', "%{$this->search}%")
        //             ->orWhere('qty', 'like', "%{$this->search}%")
        //             ->orWhere('total', 'like', "%{$this->search}%");
        //     });
        // }

        // if ($this->filterOption) {
        //     $query->where('option', $this->filterOption);
        // }

        // $data = $query->paginate(10);

        $options = Options::where('draw_id', $this->active_draw->id)
            ->where('ticket_id', $this->ticket->id)
            ->where('user_id', $this->auth_user->id)->orderBy('id', 'DESC')
            ->paginate(10);

        return view('livewire.add-ticket-form', ['options' => $options]);

    }
}
