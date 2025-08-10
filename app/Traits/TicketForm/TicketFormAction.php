<?php

namespace App\Traits\TicketForm;

use App\Models\Draw;
use App\Models\Ticket;
use Carbon\Carbon;
use Livewire\Attributes\On;

trait TicketFormAction
{
    use OptonsOperation;

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

            $this->selected_ticket = $this->user_running_ticket;
            $this->current_ticket_id = $this->user_running_ticket->id;
            $this->auth_user->draws()->syncWithoutDetaching($this->draw_id);
            $this->loadOptions(true);
            $this->loadTickets(true);
            $this->selected_draw[] = (string) $this->draw_id;
            $this->selected_draw_id = $this->draw_id;
        }

    }

    #[On('draw-selected')]
    public function handleDrawSelected($drawId, $isChecked)
    {

        if ($isChecked) {
            if (! in_array($drawId, $this->selected_draw)) {
                $this->selected_draw[] = (string) $drawId;
            }
        } elseif (! $isChecked && count($this->selected_draw) != 0) {
            $options = $this->getOptionsIntoCahe();
            if ($options && count($this->selected_draw) > 1) {
                $filteredOptions = $this->getOptionsIntoCahe()->filter(function ($option) use ($drawId) {
                    // return $option['draw_ids'] == $drawId;
                    return in_array($drawId, $option['draw_ids']);
                });
                $this->optionStoreToCache($filteredOptions);

            }

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

        $this->getTimes();
        $this->setStoreOptions($this->selected_draw);

    }

    // select ticket number
    public function handleTicketSelect($selected_ticket_id)
    {
        $this->resetError();
        $this->current_ticket_id = $selected_ticket_id;
        $this->selected_ticket = $this->auth_user->tickets()->where('id', $selected_ticket_id)->first();
        // get draw ids which is not expired
        $currentTime = Carbon::now()->timezone('Asia/Kolkata')->format('H:i');

        $option_query = $this->auth_user
            ->options()
            ->where('ticket_id', $selected_ticket_id)
            ->whereHas('draw', function ($query) use ($currentTime) {
                $query->where(function ($q) use ($currentTime) {
                    $q->where(function ($q1) use ($currentTime) {
                        $q1->where('start_time', '<=', $currentTime)
                            ->where('end_time', '>=', $currentTime);
                    })->orWhere('start_time', '>', $currentTime);
                });
            });

        $options = $option_query->get();

        if ($options->isNotEmpty()) {
            $this->clearAllOptionsIntoCache();
            $this->optionStoreToCache($options->unique('ticket_id'));
        }

        $selected_draw_ids = $options->pluck('draw_id')->unique()->values()->toArray();
        $this->selected_draw = ! empty($selected_draw_ids)
            ? $selected_draw_ids
            : [$this->draw_id];

        $this->setStoreOptions($this->selected_draw);
        $this->getTimes();
        $this->loadOptions(true);
        $this->dispatch('checked-draws', drawIds: $this->selected_draw);

    }

    public function getTimes()
    {
        $this->selected_times = Draw::whereIn('draws.id', $this->selected_draw)
            ->get()
            ->map(fn ($draw) => Carbon::createFromFormat('H:i', $draw->end_time)->format('h:i a'))
            ->implode(',');
    }
}
