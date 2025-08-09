<?php

namespace App\Traits\TicketForm;

use App\Models\Draw;
use App\Models\Options;
use App\Models\Ticket;
use App\Models\TicketOption;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
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

            $this->selected_ticket = $this->user_running_ticket;
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

    public function enterKeyPressOnAbc()
    {
        $this->dispatch('focus-qty');
    }

    public function enterKeyPressOnQty()
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

        $total = $this->abc_qty * str()->length($this->abc) * self::PRICE;
        $options = [];
        foreach (['A', 'B', 'C'] as $option) {
            $options[] = $this->addOptions($this->abc, $option, $this->abc_qty, $total);
        }
        $this->storeOptionsIntoCache($options);
        $this->abc_qty = $this->abc = '';
        $this->dispatch('focus-abc');

        $this->setStoreOptions();

    }

    public function keyEnter($row_property, $focus)
    {
        $total = $this->calculateTotal($row_property);
        if ($this->selected_draw && $this->{$row_property} && $this->{$row_property.'_qty'}) {
            $options[] = $this->addOptions($this->{$row_property}, ucfirst($row_property), $this->{$row_property.'_qty'}, $total);
            $this->storeOptionsIntoCache($options);
            $this->loadOptions(true);
            $this->dispatch($focus);
            $this->{$row_property} = '';
            $this->{$row_property.'_qty'} = '';
            $this->{'total_'.$row_property} = 0;
            $this->resetError();

        }
        $this->setStoreOptions();

    }

    public function addOptions($number, $option, $qty, $total)
    {
        return [
            'number' => $number,
            'option' => $option,
            'qty' => $qty,
            'total' => $total,
            'status' => 'RUNNING',
            'created_at' => now(),
        ];
    }

    public function storeOptionsIntoCache($data)
    {
        $options = collect($this->getOptionsIntoCahe());
        if ($options) {
            $data = $options->merge($data);
        }

        return Cache::put('options', $data->values()->all(), 7200);

    }

    public function optionStoreToCache(Collection $data)
    {
        Cache::put('options', $data->values()->all(), 7200);
    }

    public function deleteOption($index)
    {

        $data = collect($this->getOptionsIntoCahe())
            ->values();
        $data->forget($index);

        Cache::put('options', $data->values()->all());

        $this->loadOptions(true);
    }

    public function getOptionsIntoCahe()
    {
        return collect(Cache::get('options'))->sortByDesc('created_at');
    }

    public function clearAllOptionsIntoCache()
    {
        Cache::forget('options');
    }

    public function resetError()
    {
        $this->resetErrorBag(['abc', 'abc_qty', 'submit_error']);

    }

    public function submitTicket()
    {

        $digitMatrix = []; // Format: [digit][option] = count
        $selected_ticket_id = $this->current_ticket_id;

        if (count($this->stored_options) == 0) {
            $this->addError('submit_error', 'Please add at least one entry!');

            return true;
        } else {
            $this->resetError();
        }
        // delete unchecked draw's options
        // $this->auth_user->options()->whereNotIn('draw_id', $this->selected_draw)->where('ticket_id', $selected_ticket_id)->delete();
        $currentTime = Carbon::now()->timezone('Asia/Kolkata')->format('H:i');

        $options = $this->auth_user->options()
            ->where('ticket_id', $selected_ticket_id)
            ->whereIn('draw_id', $this->selected_draw)
            ->whereHas('draw', function ($query) use ($currentTime) {
                $query->where(function ($q) use ($currentTime) {
                    $q->where(function ($q1) use ($currentTime) {
                        $q1->where('start_time', '<=', $currentTime)
                            ->where('end_time', '>=', $currentTime);
                    })->orWhere('start_time', '>', $currentTime);
                });
            })
            ->delete();

        // delete unchecked draw's ticket options
        $this->auth_user->ticketOptions()
            ->where('ticket_id', $selected_ticket_id)
            ->whereIn('draw_id', $this->selected_draw)
            ->whereHas('draw', function ($query) use ($currentTime) {
                $query->where(function ($q) use ($currentTime) {
                    $q->where(function ($q1) use ($currentTime) {
                        $q1->where('start_time', '<=', $currentTime)
                            ->where('end_time', '>=', $currentTime);
                    })->orWhere('start_time', '>', $currentTime);
                });
            })
            ->delete();

        // add draw_id into $stored options
        // $checked_draw_options = [];
        // dd($this->stored_options);
        // foreach ($this->selected_draw as $draw_id) {
        //     $option = collect($this->stored_options)->map(function ($store_option) use ($draw_id, $selected_ticket_id) {
        //         $store_option['draw_id'] = $draw_id;
        //         $store_option['ticket_id'] = $selected_ticket_id;
        //         $store_option['status'] = 'COMPLETED';

        //         return $store_option;
        //     })->values()->all();
        //     $checked_draw_options = array_merge($checked_draw_options, $option);
        // }

        // dd($checked_draw_options);
        // Store options
        foreach ($this->stored_options as $option) {
            $this->auth_user->options()->create($option);
        }

        // Update user ticket status e.g. complete
        $this->auth_user->tickets()->where('id', $selected_ticket_id)->update(['status' => 'COMPLETED']);

        // Extract digit with qty from stored options
        $digitMatrix = [];

        // $this->stored_options->each(function ($opt) use (&$digitMatrix)
        foreach ($this->stored_options as $opt) {
            $option = $opt['option'];
            $digits = str_split((string) $opt['number']);
            $qty = $opt['qty'];

            foreach ($digits as $digit) {
                if (! isset($digitMatrix[$digit][$option])) {
                    $digitMatrix[$digit][$option] = 0;
                }
                $digitMatrix[$digit][$option] += $qty;

            }
        }

        ksort($digitMatrix);

        // Store Ticket Option
        foreach ($this->selected_draw as $draw_id) {
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
                        'draw_id' => $draw_id,
                        'ticket_id' => $selected_ticket_id,
                        'number' => $number,
                        'a_qty' => $options['A'],
                        'b_qty' => $options['B'],
                        'c_qty' => $options['C'],
                    ]
                );

            }
        }

        if (! $this->is_edit_mode) {
            // Generate new Ticket
            // $this->addTicket();
            $this->dispatch('refresh-window');
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
            $options = $this->getOptionsIntoCahe();
            if ($options && count($this->selected_draw) > 1) {
                $filteredOptions = $this->getOptionsIntoCahe()->filter(function ($option) use ($drawId) {
                    return $option['draw_id'] == $drawId;
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

        if (count($this->selected_draw) > 1) {
            logger()->info(count($this->selected_draw));
            $this->setStoreOptions();
        }
    }

    // select ticket number
    public function handleTicketSelect($selected_ticket_id)
    {

        $this->current_ticket_id = $selected_ticket_id;
        $this->selected_ticket = $this->auth_user->tickets()->where('id', $selected_ticket_id)->first();
        // get draw ids which is not expired
        $currentTime = Carbon::now()->timezone('Asia/Kolkata')->format('H:i');

        $selected_draw_ids = $this->auth_user->options()->where('ticket_id', $selected_ticket_id)
            ->whereHas('draw', function ($query) use ($currentTime) {
                $query->where(function ($q) use ($currentTime) {
                    $q->where(function ($q1) use ($currentTime) {
                        $q1->where('start_time', '<=', $currentTime)
                            ->where('end_time', '>=', $currentTime);
                    })->orWhere('start_time', '>', $currentTime);
                });
            })->groupBy('draw_id')
            ->pluck('draw_id')->toArray();

        if (count($selected_draw_ids) > 0) {
            $this->selected_draw = $selected_draw_ids;

            // $this->selected_draw = array_unique(array_merge($this->selected_draw, $selected_draw_ids));

        } else {
            $this->selected_draw = [$this->draw_id];

        }
        $this->loadOptions(true);
        $this->dispatch('checked-draws', drawIds: $this->selected_draw);
    }

    public function setStoreOptions()
    {
        $selected_draw_ids = $this->selected_draw;
        $selected_ticket_id = $this->current_ticket_id;
        $checked_draw_options = [];

        foreach ($selected_draw_ids as $draw_id) {
            // Use original stored_options without filtering
            $option = $this->getOptionsIntoCahe()
                ->map(function ($store_option) use ($draw_id, $selected_ticket_id) {
                    $store_option['draw_id'] = $draw_id;
                    $store_option['ticket_id'] = $selected_ticket_id;
                    $store_option['status'] = 'COMPLETED';

                    return $store_option;
                })
                ->values()
                ->all();

            $checked_draw_options = array_merge($checked_draw_options, $option);
        }
        Cache::put('options', $checked_draw_options, 7200);
        // $this->stored_options = $checked_draw_options;
        $this->loadOptions(true);

    }
}
