<?php

namespace App\Traits\TicketForm;

use App\Models\DrawDetail;
use App\Models\Ticket;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

trait TicketFormPagination
{
    public int $ticket_page = 1;

    public int $draw_page = 1;

    public int $option_page = 1;

    public int $cross_abc_page = 1;

    public $draw_list = [];

    public $ticket_list = [];

    public $option_list = [];

    public $latest_draw_list = [];

    public $latestDrawPerPage = 10;

    public int $latest_draw_page = 1;

    public array $latest_draw_ids = [];

    public array $selected_draw = [];

    public $drawPerPage = 12;

    public int $ticketPerPage = 10;

    public int $optionPerPage = 10;

    public int $crossAbcPerPage = 10;

    public $hasMoreDrawPages = true;

    public $loaded_tickets_ids = [];

    public $loaded_draw_ids = [];

    public $selected_ticket = '';

    public int $total_options = 0;

    public array $stored_cross_abc_data = [];

    public int $total_cross_data = 0;

    public function updatedDrawPage()
    {
        $this->loadDraws(); // called when `page` changes from Alpine
    }

    public function updatedTicketPage()
    {
        $this->loadTickets();
    }

    public function updatedLatestDrawPage()
    {
        $this->loadLatestDraws();
    }

    public function updatedOptionPage()
    {
        $this->loadOptions();
    }

    public function updatedCrossAbcPage()
    {
        $this->loadAbcData();
    }

    public function loadDraws()
    {
        $current_time = Carbon::now()->timezone('Asia/Kolkata')->format('H:i');

        $drawQuery = DrawDetail::whereDate('date', now())
            ->where(function ($q) use ($current_time) {
                $q->where(function ($inner) use ($current_time) {
                    $inner->where('start_time', '<=', $current_time)
                        ->where('end_time', '>=', $current_time);
                })
                    ->orWhere('start_time', '>', $current_time);
            });

        $count = (clone $drawQuery)->count();
        $this->drawPerPage = $count <= 10 ? 10 : $this->drawPerPage;

        $paginatedDraws = $drawQuery->orderByRaw('
        CASE
            WHEN start_time <= ? AND end_time >= ? THEN 0
            ELSE 1
        END, start_time
    ', [$current_time, $current_time])
            ->paginate($this->drawPerPage, ['*'], 'draw_page', $this->draw_page);
        $newDraws = collect($paginatedDraws->items());

        $uniqueDraws = $newDraws->reject(function ($draw) {
            return in_array($draw['id'], $this->loaded_draw_ids, true);
        })->values()->all();

        foreach ($uniqueDraws as $uniqueDraw) {
            $this->loaded_draw_ids[] = $uniqueDraw['id'];
        }

        $this->draw_list = array_merge($this->draw_list, $uniqueDraws);
        $this->hasMoreDrawPages = $paginatedDraws->hasMorePages();
    }

    public function loadLatestDraws()
    {
        $current_time = Carbon::now()->timezone('Asia/Kolkata')->format('H:i');

        $drawQuery = DrawDetail::whereHas('crossAbcDetail', function ($q) {
            return $q->where('user_id', $this->auth_user->id)
                ->whereDate('created_at', Carbon::today());
        }
        )
            ->whereHas('ticketOptions', function ($q) {
                return $q->where('user_id', $this->auth_user->id)
                    ->whereDate('created_at', Carbon::today());
            }
            )
            ->with([
                'crossAbcDetail' => fn ($q) => $q->where('user_id', $this->auth_user->id),
                'ticketOptions' => fn ($q) => $q->where('user_id', $this->auth_user->id),
            ]);

        $count = (clone $drawQuery)->count();
        $this->latestDrawPerPage = $count <= 10 ? 10 : $this->latestDrawPerPage;

        $paginatedDraws = $drawQuery->orderBy('updated_at', 'DESC')
            ->paginate($this->latestDrawPerPage, ['*'], 'latest_draw_page', $this->latest_draw_page);
        $newDraws = collect($paginatedDraws->items());

        $uniqueDraws = $newDraws->reject(function ($draw) {
            return in_array($draw['id'], $this->latest_draw_ids, true);
        })->values()->all();

        foreach ($uniqueDraws as $uniqueDraw) {
            $this->latest_draw_ids[] = $uniqueDraw['id'];
        }

        $this->latest_draw_list = array_merge($this->latest_draw_list, $uniqueDraws);
        // $this->hasMoreDrawPages = $paginatedDraws->hasMorePages();
    }

    public function loadTickets($reset = false)
    {
        if ($reset) {
            $this->ticket_page = 1;
            $this->ticket_list = [];
            $this->loaded_tickets_ids = [];
        }
        $query = Ticket::query()
            ->whereDate('created_at', Carbon::today())
            ->where('status', '!=', 'COMPLETED')
            ->forUser($this->auth_user->id);

        $count = (clone $query)->count();
        $this->ticketPerPage = $count <= 5 ? 10 : $this->ticketPerPage;

        $paginatedTickets = $query->orderBy('id', 'desc')
            ->paginate($this->ticketPerPage, ['*'], 'ticket_page', $this->ticket_page);

        $newTickets = collect($paginatedTickets->items());
        $uniqueTickets = $newTickets->reject(function ($ticket) {
            return in_array($ticket['id'], $this->loaded_tickets_ids, true);
        })->values()->all();

        foreach ($uniqueTickets as $uniqueTicket) {
            $this->loaded_tickets_ids[] = $uniqueTicket['id'];
        }
        $this->ticket_list = array_merge($this->ticket_list, $uniqueTickets);
    }

    public function collectionPage($items, $perPage = 10, $page = 1)
    {
        $items = $items instanceof Collection ? $items : $items;
        $items = $items->values();
        $data = $items->slice(($page - 1) * $perPage, $perPage);

        return $data->values()->all();
    }

    public function loadOptions($reset = false)
    {
        if ($reset) {
            $this->option_page = 1; // Reset page to 1
            $this->stored_options = [];
        }
        $options = $this->getOptionsIntoCache();

        if ($options) {
            if (count($this->stored_options) >= count($options)) {
                return true; // already loaded all
            }

            if (count($options) <= 10) {
                $this->optionPerPage = 10;
                $this->option_page = 1;
            }

            $newData = $this->collectionPage(
                $options,
                $this->optionPerPage,
                $this->option_page
            );

            if ($reset) {
                $this->stored_options = $newData;
            } else {
                $this->stored_options = array_merge($this->stored_options, $newData);
            }

        }
        // $newOptions = Options::forTicket($this->current_ticket_id)
        //     ->forUser($this->auth_user->id)
        //     ->orderBy('id', 'DESC')->get();

    }

    public function loadAbcData($reset = false)
    {
        if ($reset) {
            $this->cross_abc_page = 1; // Reset page to 1
            $this->stored_cross_abc_data = [];
        }
        $crossAbcData = $this->getCrossOptions();

        if ($crossAbcData) {
            if (count($this->stored_cross_abc_data) >= count($crossAbcData)) {
                return true; // already loaded all
            }

            if (count($crossAbcData) <= 10) {
                $this->crossAbcPerPage = 10;
                $this->cross_abc_page = 1;
            }

            $newData = $this->collectionPage(
                $crossAbcData,
                $this->crossAbcPerPage,
                $this->cross_abc_page
            );

            if ($reset) {
                $this->stored_cross_abc_data = $newData;
            } else {
                $this->stored_cross_abc_data = array_merge($this->stored_cross_abc_data, $newData);
            }

        }

    }
}
