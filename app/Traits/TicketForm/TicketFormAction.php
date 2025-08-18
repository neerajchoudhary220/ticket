<?php

namespace App\Traits\TicketForm;

use App\Models\Draw;
use App\Models\DrawDetail;
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

        $this->active_draw = DrawDetail::runningDraw()->first();
        if ($this->active_draw) {
            // $this->active_draw_number = $this->active_draw->draw_number;
            $this->draw_detail_id = $this->active_draw->id;

            $endTime = Carbon::createFromFormat('H:i', $this->active_draw->end_time);
            $current_time = Carbon::now()->setTimezone('Asia/Kolkata')->format('h:i A');
            $this->end_time = $endTime->format('h:i A');
            $this->duration = $endTime->diffInMinutes($current_time, true);
            $this->user_running_ticket = Ticket::firstOrCreate([
                'user_id' => $this->auth_user->id,
                'draw_detail_id' => $this->active_draw->id,
                'status' => 'RUNNING',

            ],
                [
                    'ticket_number' => $ticketNumber,
                ]);

            $this->selected_ticket = $this->user_running_ticket;
            $this->current_ticket_id = $this->user_running_ticket->id;
            // $this->auth_user->drawDetails()->syncWithoutDetaching($this->draw_detail_id);
            $this->loadOptions(true);
            $this->loadTickets(true);
            $this->selected_draw[] = (string) $this->draw_detail_id;
            $this->selected_draw_id = $this->draw_detail_id;
        }

    }

    #[On('draw-selected')]
    public function handleDrawSelected($draw_detail_id, $isChecked)
    {
        if ($isChecked) {
            if (! in_array($draw_detail_id, $this->selected_draw)) {
                $this->selected_draw[] = (string) $draw_detail_id;
            }
        } elseif (! $isChecked && count($this->selected_draw) != 0) {
            $options = $this->getOptionsIntoCache();
            if ($options && count($this->selected_draw) > 1) {
                $filteredOptions = $this->getOptionsIntoCache()->filter(function ($option) use ($draw_detail_id) {
                    return in_array($draw_detail_id, $option['draw_details_ids']);
                });
                $this->optionStoreToCache($filteredOptions);

            }

            $this->selected_draw = array_filter(
                $this->selected_draw,
                fn ($id) => $id != $draw_detail_id
            );
        }

        $total_selected_draws = count($this->selected_draw);

        $this->dispatch('check-selected-draw',
            total_selected_draw: $total_selected_draws,
            draw_details_id: $draw_detail_id
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

        $drawIds = $this->getActiveDrawIds();
        // get options
        $option_query = $this->auth_user->options()
            ->where('ticket_id', $selected_ticket_id)
            ->where(function ($query) use ($drawIds) {
                foreach ($drawIds as $id) {
                    $query->orWhereJsonContains('draw_details_ids', $id);
                }
            });

        $cross_abc_query = $this->auth_user->crossAbc()
            ->where('ticket_id', $selected_ticket_id)
            ->where(function ($query) use ($drawIds) {
                foreach ($drawIds as $id) {
                    $query->orWhereJsonContains('draw_details_ids', $id);
                }
            });
        $options = $option_query->get(); // empty

        $cross_abc = $cross_abc_query->get(); // empty
        $this->clearAllOptionsIntoCache();
        $this->clearAllCrossAbcIntoCache();

        if ($options->isNotEmpty()) {
            $this->optionStoreToCache($options);
            $selected_draw_ids = $options
                ->pluck('draw_details_ids')      // [[56,58], [58,59], ...]
                ->flatten()              // [56,58,58,59,...]
                ->unique()
                ->intersect($drawIds)    // keep only active draw IDs
                ->values();
        }

        if ($cross_abc->isNotEmpty()) {
            $this->storeCrossAbcIntoCache($cross_abc);
            $selected_draw_ids = $options
                ->pluck('draw_details_ids')      // [[56,58], [58,59], ...]
                ->flatten()              // [56,58,58,59,...]
                ->unique()
                ->intersect($drawIds)    // keep only active draw IDs
                ->values();
        }

        // reset keys

        $this->selected_draw = ! empty($selected_draw_ids)
            ? $selected_draw_ids->toArray()
            : [$this->draw_detail_id];
        $this->setStoreOptions($this->selected_draw);
        $this->getTimes();
        $this->loadOptions(true);
        $this->loadAbcData(true);
        $this->dispatch('checked-draws', drawIds: $this->selected_draw);

    }

    public function getTimes()
    {
        $this->selected_times = DrawDetail::whereIn('id', $this->selected_draw)
            ->get()
            ->map(fn ($draw) => Carbon::createFromFormat('H:i', $draw->end_time)->format('h:i a'))
            ->implode(',');
        $this->calculateFinalTotal();

    }

    public function calculateFinalTotal()
    {
        $total_stored_options = collect($this->stored_options)->sum('total');
        $total_selected_times = $this->selected_draw ? count($this->selected_draw) : 0;
        $this->final_total_qty = $total_stored_options * $total_selected_times;
    }
}
