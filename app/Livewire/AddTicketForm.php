<?php

namespace App\Livewire;

use App\Models\User;
use App\Traits\TicketForm\TicketFormAction;
use App\Traits\TicketForm\TicketFormPagination;
use Illuminate\Http\Request;
use Livewire\Component;
use Livewire\WithPagination;

class AddTicketForm extends Component
{
    use TicketFormAction,TicketFormPagination,WithPagination;

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

    public $abc;

    public $abc_qty;

    // protected $updatesQueryString = ['search', 'filterOption'];

    public function mount(Request $request, $ticket = null)
    {
        $this->auth_user = User::find($request->user()->id);
        if ($ticket) {
            $this->draw_id = $ticket->draw->id;
            $this->current_ticket_id = $ticket->id;
            $this->user_running_ticket = $ticket;
            $this->is_edit_mode = true;

            $this->selected_draw[] = $this->draw_id;
        } else {
            $this->addTicket();
        }
        $this->loadDraws();
        $this->loadTickets();
    }

    public function render()
    {
        return view('livewire.add-ticket-form');

    }
}
