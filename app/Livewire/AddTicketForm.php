<?php

namespace App\Livewire;

use App\Models\Draw;
use App\Models\Options;
use App\Models\Ticket;
use App\Models\User;
use App\Traits\TicketForm\TicketFormAction;
use Illuminate\Http\Request;
use Livewire\Component;
use Livewire\WithPagination;

class AddTicketForm extends Component
{
    use TicketFormAction,WithPagination;

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

    public $abc;

    public $abc_qty;

    public int $ticket_page = 1;

    public int $draw_page = 1;

    public int $option_page = 1;

    public $draw_list = [];

    public $ticket_list = [];

    public $option_list = [];

    public array $selected_draw = [];

    public $drawPerPage = 5;

    public int $ticketPerPage = 3;

    public int $optionPerPage = 5;
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

            $this->selected_draw[] = $this->draw_id;
        } else {
            $this->addTicket();

        }
    }

    public function updatedDrawPage()
    {
        $this->loadDraws(); // called when `page` changes from Alpine
    }

    public function updatedTicketPage()
    {
        $this->loadTickets();
    }

    public function updatedOptionPage()
    {
        $this->loadOptions();
    }

    public function loadDraws()
    {
        $newDraws = Draw::orderBy('end_time', 'desc')->paginate($this->drawPerPage, ['*'], 'page', $this->draw_page);

        // Append instead of replace
        $this->draw_list = array_merge($this->draw_list, $newDraws->items());
    }

    public function loadTickets()
    {
        $newTickets = Ticket::forDraw($this->draw_id)
            ->forUser($this->auth_user->id)
            ->orderBy('id', 'desc')
            ->paginate(5, ['*'], 'ticket_page', $this->ticket_page);
        $this->ticket_list = array_merge($this->ticket_list, $newTickets->items());
    }

    public function loadOptions($reset = false)
    {
        if ($reset) {
            $this->option_page = 1; // Reset page to 1
            $this->option_list = []; // Clear existing list
        }
        $newOptions = Options::forDraw($this->draw_id)
            ->forTicket($this->current_ticket_id)
            ->forUser($this->auth_user->id)
            ->orderBy('id', 'DESC')
            ->paginate(5, ['*'], 'option_page', $this->option_page);

        $this->option_list = array_merge($this->option_list, $newOptions->items());
    }

    public function render()
    {
        return view('livewire.add-ticket-form');

    }
}
