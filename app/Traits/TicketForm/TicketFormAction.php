<?php

namespace App\Traits\TicketForm;

use App\Models\Draw;
use App\Models\Options;
use App\Models\Ticket;
use App\Models\TicketOption;
use Carbon\Carbon;
use Livewire\Attributes\On;

trait TicketFormAction
{
    const PRICE = 11;

    public int $selected_draw_id;

    public $submit_error = '';

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

        if ($this->active_draw) {
            $this->active_draw_number = $this->active_draw->draw_number;
            $this->draw_id = $this->active_draw->id;

            $endTime = Carbon::createFromFormat('H:i', $this->active_draw->end_time);
            $current_time = Carbon::now()->setTimezone('Asia/Kolkata')->format('h:i A');
            $this->end_time = $endTime->format('h:i A');
            $this->duration = $endTime->diffInMinutes($current_time, true);

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
            $this->loadOptions(true);
            $this->loadTickets(true);
            $this->selected_draw[] = $this->draw_id;
            $this->selected_draw_id = $this->draw_id;
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

            $this->loadOptions(true);
            $this->resetError();

        }

    }

    public function resetError()
    {
        $this->resetErrorBag(['abc', 'abc_qty', 'submit_error']);

    }

    public function applyHandle()
    {
        $hasError = false;

        // Validate abc
        if (empty($this->abc)) {
            $this->addError('abc', 'Please Enter The Value');
            $hasError = true;
        }

        // Validate abc_qty
        if (empty($this->abc_qty)) {
            $this->addError('abc_qty', 'Please Enter Qty');
            $hasError = true;
        } elseif ($this->abc_qty <= 0) {
            $this->addError('abc_qty', 'Qty must be greater than 0');
            $hasError = true;
        }

        // If errors exist, return early
        if ($hasError) {
            return true;
        }
        $this->resetError();

        // Clear previous errors if validation passes

        $total = $this->abc_qty * $this->abc * self::PRICE;

        // if($this->abc_qty)
        foreach (['A', 'B', 'C'] as $option) {
            Options::create([
                'user_id' => $this->auth_user->id,
                'draw_id' => $this->draw_id,
                'ticket_id' => $this->current_ticket_id,
                'number' => $this->abc,
                'option' => $option,
                'qty' => $this->abc_qty,
                'total' => $total,
                'status' => 'RUNNING',
            ]);
        }

        $this->abc_qty = $this->abc = '';
        $this->loadOptions(true);

    }

    public function deleteOption(Options $option, $index)
    {

        $option->delete();
        unset($this->option_list[$index]);
        $this->loadOptions(true);

    }

    public function submitTicket()
    {

        $digitMatrix = []; // Format: [digit][option] = count

        $last_completed_options = Options::query()->forUser($this->auth_user->id)
            ->forDraw($this->draw_id)
            ->where('ticket_id', $this->current_ticket_id);

        if (count($last_completed_options->get()) == 0) {
            $this->addError('submit_error', 'Please add at least one entry!');

            return true;
        } else {
            $this->resetError();
        }
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

        TicketOption::forUser($this->auth_user->id)->forTicket($this->current_ticket_id)->forDraw($this->draw_id)
            ->delete();
        $digitMatrix = [];

        $last_completed_options->forCompleted()
            ->get()->each(function ($opt) use (&$digitMatrix) {
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

    #[On('draw-selected')]
    public function handleDrawSelected($drawId, $isChecked)
    {

        if ($isChecked) {
            if (! in_array($drawId, $this->selected_draw)) {
                $this->selected_draw[] = $drawId;
            }
        } elseif (! $isChecked && count($this->selected_draw) != 0) {
            $this->selected_draw = array_filter(
                $this->selected_draw,
                fn ($id) => $id != $drawId
            );
        }

        $total_selected_draws = count($this->selected_draw);

        $this->dispatch('check-selected-draw',
            total_selected_draw: $total_selected_draws,
            drawId: $drawId
        );
    }
}
