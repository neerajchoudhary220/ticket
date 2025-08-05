<?php

namespace App\Livewire;

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
