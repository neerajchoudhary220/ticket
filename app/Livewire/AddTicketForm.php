<?php

namespace App\Livewire;

use App\Models\Draw;
use App\Models\Options;
use App\Models\Ticket;
use App\Models\TicketOption;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
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

    public $user_running_ticket;

    public $search = '';

    public $filterOption = '';

    public $ticketNumber = '';

    public $current_ticket_id = '';

    public $draw_id = '';

    public $is_edit_mode = false;

    public $selected_ticket_id;
    // protected $updatesQueryString = ['search', 'filterOption'];

    public function mount(Request $request, $ticket = null)
    {
        $this->auth_user = User::find($request->user()->id);
        if ($ticket) {
            $this->draw_id = $ticket->draw->id;
            $this->current_ticket_id = $ticket->id;
            $this->selected_ticket_id = $ticket->id;
            $this->user_running_ticket = $ticket;
            $this->is_edit_mode = true;
        } else {
            $this->addTicket();

        }
    }

    protected function addTicket()
    {

        if ($this->auth_user->tickets->last() && $this->auth_user->tickets->last()->ticket_number) {
            $last_ticket_number = explode('-', $this->auth_user->tickets->last()->ticket_number);
            $ticketNumber = $last_ticket_number[0].'-'.(int) $last_ticket_number[1] + 1;
        } else {
            $series = explode('-', $this->auth_user->ticket_series);
            $ticketNumber = $series[0].'-'.(int) ($series[1]) + 1;
        }

        $this->active_draw = Draw::runningDraw()->first();
        $this->active_draw_number = $this->active_draw->draw_number;
        $this->draw_id = $this->active_draw->id;

        $endTime = Carbon::createFromFormat('H:i', $this->active_draw->end_time);
        $current_time = Carbon::now()->setTimezone('Asia/Kolkata')->format('h:i A');
        $this->end_time = $endTime->format('h:i A');
        $this->duration = $endTime->diffInMinutes($current_time, true);

        if ($this->active_draw) {
            $this->user_running_ticket = Ticket::firstOrCreate([
                'user_id' => $this->auth_user->id,
                'draw_id' => $this->active_draw->id,
                'status' => 'RUNNING',

            ],
                [
                    'ticket_number' => $ticketNumber,
                ]);
            $this->current_ticket_id = $this->user_running_ticket->id;
            $this->auth_user->draws()->syncWithoutDetaching($this->draw_id);

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
        if ($this->draw_id && $this->{$row_property} && $this->{$row_property.'_qty'}) {
            Options::create([
                'user_id' => $this->auth_user->id,
                'draw_id' => $this->draw_id,
                'ticket_id' => $this->current_ticket_id,
                'number' => $this->{$row_property},
                'option' => ucfirst($row_property),
                'qty' => $this->{$row_property.'_qty'},
                'total' => $total,
                'status' => 'RUNNING',
            ]);
            $this->dispatch($focus);
            $this->{$row_property} = '';
            $this->{$row_property.'_qty'} = '';
            $this->{'total_'.$row_property} = 0;

        }

    }

    // public function handleSelectedTicket($id)
    // {
    //     logger()->info('Selected ticket ID: '.$id);
    // }

    public function deleteOption(Options $option)
    {
        $option->delete();
    }

    public function submitTicket()
    {
        $digitMatrix = []; // Format: [digit][option] = count

        // Update Ticket Status with Completed status
        $this->auth_user->tickets()
            ->where('id', $this->current_ticket_id)
            ->where('draw_id', $this->draw_id)
            ->running()->update([
                'status' => 'COMPLETED',
            ]);
        // update Options Status with Completed status
        Options::forUser($this->auth_user->id)
            ->where('draw_id', $this->draw_id)
            ->where('ticket_id', $this->current_ticket_id)->update(['status' => 'COMPLETED']);

        // Get All Completed Options
        $last_completed_options = Options::forUser($this->auth_user->id)
            ->where('draw_id', $this->draw_id)
            ->where('status', 'COMPLETED')
            ->where('ticket_id', $this->current_ticket_id)->get();

        TicketOption::forUser($this->auth_user->id)->forTicket($this->current_ticket_id)->forDraw($this->draw_id)
            ->delete();
        $digitMatrix = [];

        $last_completed_options->each(function ($opt) use (&$digitMatrix) {
            $option = $opt->option;
            $digits = str_split((string) $opt->number);
            $qty = $opt->qty;

            foreach ($digits as $digit) {
                if (! isset($digitMatrix[$digit][$option])) {
                    $digitMatrix[$digit][$option] = 0;
                }
                $digitMatrix[$digit][$option] += $qty;

            }
        });

        ksort($digitMatrix);
        foreach ($digitMatrix as $number => $options) {

            if (! isset($options['A'])) {
                $options['A'] = 0;
            }
            if (! isset($options['B'])) {
                $options['B'] = 0;
            }
            if (! isset($options['C'])) {
                $options['C'] = 0;
            }
            TicketOption::updateOrCreate(
                [
                    'user_id' => $this->auth_user->id,
                    'draw_id' => $this->draw_id,
                    'ticket_id' => $this->current_ticket_id,
                    'number' => $number,
                    'a_qty' => $options['A'],
                    'b_qty' => $options['B'],
                    'c_qty' => $options['C'],
                ]
            );

        }
        if (! $this->is_edit_mode) {
            // Generate new Ticket
            $this->addTicket();
        } else {
            return redirect()->route('dashboard');
        }

    }

    public function render()
    {

        $ticket_list = Ticket::forDraw($this->draw_id)->forUser($this->auth_user->id)->get();
        $options = Options::where('draw_id', $this->draw_id)
            ->where('ticket_id', $this->current_ticket_id)
            ->where('user_id', $this->auth_user->id)->orderBy('id', 'DESC')
            ->paginate(5);

        return view('livewire.add-ticket-form', ['options' => $options, 'ticket_list' => $ticket_list]);

    }
}
