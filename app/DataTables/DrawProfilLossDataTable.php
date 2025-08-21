<?php

namespace App\DataTables;

use App\Models\DrawDetail;
use App\Traits\CalculatePL;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class DrawProfilLossDataTable extends DataTable
{
    use CalculatePL;

    /**
     * Build the DataTable class.
     *
     * @param  QueryBuilder<DrawDetail>  $query
     */
    protected function isAdminSeg()
    {
        return request()->segment(1) === 'admin';
    }

    protected function getCrossClaim($draw_detail)
    {
        $ab_claim = $draw_detail->ab;
        $ac_claim = $draw_detail->ac;
        $bc_claim = $draw_detail->bc;
        if (request()->segment(1) != 'admin') {
            $ab_claim = $draw_detail->crossAbcDetail()->where('user_id', auth()->id())
                ->where('number', $ab_claim)
                ->where('type', 'AB')->sum('amount');
            $ac_claim = $draw_detail->crossAbcDetail()->where('user_id', auth()->id())
                ->where('number', $ac_claim)
                ->where('type', 'AC')->sum('amount');
            $bc_claim = $draw_detail->crossAbcDetail()->where('user_id', auth()->id())
                ->where('number', $bc_claim)
                ->where('type', 'BC')->sum('amount');

            return $ab_claim + $ac_claim + $bc_claim;
        }

        return $draw_detail->claim_ab + $draw_detail->claim_ac + $draw_detail->claim_bc;
    }

    protected function getCrossAmt($draw_detail)
    {

        if (! $this->isAdminSeg()) {
            return $draw_detail->crossAbcDetail()
                ->where('user_id', auth()->user()->id)
                ->sum('amount');
        }

        return $draw_detail->cross_amt;

    }

    protected function getClaim($draw_detail)
    {
        if ($this->isAdminSeg()) {
            return $draw_detail->claim;
        } else {
            $a_claim = $draw_detail->claim_a;
            $b_claim = $draw_detail->claim_b;
            $c_claim = $draw_detail->claim_c;

            DB::enableQueryLog();
            $a_qty = $draw_detail->
            ticketOptions()->where('user_id', auth()->user()->id)
                ->where('number', $a_claim)
                ->sum('a_qty');
            logger()->info(DB::getQueryLog());
            $b_qty = $draw_detail->
         ticketOptions()->where('user_id', auth()->user()->id)
             ->where('number', $b_claim)
             ->sum('b_qty');

            $c_qty = $draw_detail->
         ticketOptions()->where('user_id', auth()->user()->id)
             ->where('number', $c_claim)
             ->sum('c_qty');

            return $a_qty + $b_qty + $c_qty;
            // ->claim_a_qty + $draw_detail->ticketOption->claim_b_qty + $draw_detail->ticketOption->claim_c_qty;

        }
    }

    protected function getTq($draw_detail)
    {

        if (! $this->isAdminSeg()) {
            $a_qty = $draw_detail->ticketOptions()->where('user_id', auth()->user()->id)->sum('a_qty');
            $b_qty = $draw_detail->ticketOptions()->where('user_id', auth()->user()->id)->sum('b_qty');
            $c_qty = $draw_detail->ticketOptions()->where('user_id', auth()->user()->id)->sum('c_qty');

            return $a_qty + $b_qty + $c_qty;
        }

        return $draw_detail->tq;

    }

    public function dataTable(QueryBuilder $query, Request $request): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->editColumn('end_time', function ($draw_detail) {
                $end_time = Carbon::parse($draw_detail->end_time)->format('h:i a');
                $url = request()->segment(1) === 'admin'
                    ? route('admin.draw.detail.list', $draw_detail->id)
                    : route('dashboard.draw.details.list', ['draw_detail_id' => $draw_detail->id]);

                return "<a href='$url' class='text-primary h6'>$end_time</a>";
            })
            ->filterColumn('end_time', function ($query, $keyword) {
                $keyword = strtolower($keyword);
                $query->whereRaw("TIME_FORMAT(end_time, '%h:%i %p') LIKE ?", ["%{$keyword}%"])
                    ->orWhereRaw("TIME_FORMAT(end_time, '%l %p') LIKE ?", ["%{$keyword}%"])
                    ->orWhereRaw("TIME_FORMAT(end_time, '%H:%i') LIKE ?", ["%{$keyword}%"]);
            })
            ->filterColumn('tq', function ($query, $keyword) {
                // tq is derived column, so handle manually
                $query->whereRaw('CAST(total_qty AS CHAR) LIKE ?', ["%{$keyword}%"]);
            })
            ->filterColumn('cross_amt', function ($query, $keyword) {
                $query->whereRaw('CAST(total_cross_amt AS CHAR) LIKE ?', ["%{$keyword}%"]);
            })
            ->filterColumn('p_and_l', function ($query, $keyword) {
                $query->whereRaw('
        (
            (COALESCE(total_qty,0) * 11) 
            - (COALESCE(claim,0) * 100) 
            + COALESCE(total_cross_amt,0) 
            - ((COALESCE(claim_ab,0) + COALESCE(claim_ac,0) + COALESCE(claim_bc,0)) * 100)
        ) LIKE ?
    ', ["%{$keyword}%"]);
            })
            ->filterColumn('cross_claim', function ($query, $keyword) {
                $query->whereRaw('((claim_ab + claim_ac + claim_bc) * 100) LIKE ?', ["%{$keyword}%"]);
            })

            ->editColumn('tq', function ($draw_detail) {
                $tq = $this->getTq($draw_detail);
                $url = $this->isAdminSeg() ? route('admin.dashboard.total.qty.details.list', $draw_detail->id)
                : route('dashboard.draw.total.qty.list.details', $draw_detail->id);

                return "<a href='$url' class='text-primary h6'>{$tq}</a>";
            })
            ->editColumn('cross_amt', function ($draw_detail) {
                $cross_amt = $this->getCrossAmt($draw_detail);
                $url = $this->isAdminSeg() ? route('admin.dashboard.cross.abc', ['draw_detail_id' => $draw_detail->id])
                : route('dashboard.draw.cross.abc.details.list', ['draw_detail_id' => $draw_detail->id]);

                return "<a href='$url' class='text-primary h6'>{$cross_amt}</a>";
            })
            ->editColumn('cross_claim', function ($draw_detail) {
                return $this->getCrossClaim($draw_detail);
            })
            ->editColumn('claim', function ($draw_detail) {
                return $this->getClaim($draw_detail);
            })
            ->editColumn('p_and_l', function ($row) {
                // $p_and_l = (int) $row->p_and_l;
                $tq = $this->getTq($row) * 11;
                $cross_amt = $this->getCrossAmt($row);
                $claim = $this->getClaim($row);
                $crossClaim = $this->getCrossClaim($row);
                $p_and_l = $this->calculateProfitAndLoss($tq, $cross_amt, $claim, $crossClaim);
                $bgClass = $p_and_l < 0 ? 'bg-danger text-white' : 'bg-success text-white';
                if ($p_and_l == 0) {
                    $bgClass = 'text-dark';
                }

                return "<div class='{$bgClass} text-center'>{$p_and_l}</div>";
            })
            ->editColumn('created_at', fn ($row) => Carbon::parse($row->created_at)->format('Y-m-d'))
            ->addColumn('action', function ($draw_detail) {
                $draw_details = route('dashboard.draw.details.list', ['draw_id' => $draw_detail->id]);
                $draw_detail_id = $draw_detail->id;
                $end_time = Carbon::createFromTimeString($draw_detail->end_time)->format('H:i');
                $now = Carbon::now()->setSecond(0)->timezone('Asia/Kolkata');
                $segment = request()->segment(1);

                if ($draw_detail->claim <= 0 &&
                $segment === 'admin' && $now->gte($end_time)
                && ($draw_detail->total_qty != 0 || $draw_detail->total_cross_amt != 0)
                && (empty($draw_detail->claim_ab) && empty($draw_detail->claim_ac) && empty($draw_detail->claim_bc))) {

                    return <<<HTML
                <div class="d-flex justify-content-center">
        <button class="btn btn-warning addClaim ms-3 text-white" data-draw-detail-id="{$draw_detail_id}">Claim</button>
            </div>
    HTML;
                }

                return '--';

            })
            ->rawColumns(['end_time', 'tq', 'cross_amt', 'p_and_l', 'action', 'cross_claim', 'claim']);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(DrawDetail $model, Request $request): QueryBuilder
    {
        return $model->newQuery()
            ->select('draw_details.*')
            // ✅ Pre-computed values for ordering/searching
            ->selectRaw('COALESCE(total_qty,0) as tq')
            ->selectRaw('(COALESCE(total_qty,0) * 11) as t_amt')
            // ->selectRaw('COALESCE(claim,0) as claim')
            ->selectRaw('(COALESCE(claim,0) * 100) as c_amt')
            ->selectRaw('COALESCE(total_cross_amt,0) as cross_amt')
            ->selectRaw('(COALESCE(claim_ab,0) + COALESCE(claim_ac,0) + COALESCE(claim_bc,0)) as cross_claim')
            ->selectRaw('
                ((COALESCE(total_qty,0) * 11) 
                - (COALESCE(claim,0) * 100) 
                + COALESCE(total_cross_amt,0) 
                - ((COALESCE(claim_ab,0) + COALESCE(claim_ac,0) + COALESCE(claim_bc,0)) * 100)
                ) as p_and_l
            ')
            // ✅ Date filters
            ->when(! $request->get('start_date') && ! $request->get('end_date') && ! $request->get('da7'),
                fn ($q) => $q->whereDate('date', now())
            )
            ->when($request->filled('start_date') && $request->filled('end_date'),
                fn ($q) => $q->whereBetween('date', [$request->get('start_date'), $request->get('end_date')])
            )
            ->when($request->filled('start_date') && ! $request->filled('end_date'),
                fn ($q) => $q->whereDate('date', $request->get('start_date'))
            )
            ->when($request->filled('day'), function ($q) use ($request) {
                return match ($request->get('day')) {
                    'Today' => $q->whereDate('date', now()),
                    'Yesterday' => $q->whereDate('date', now()->subDay()),
                    'Last 7 Days' => $q->whereBetween('date', [now()->subDays(6), now()]),
                    'Last 30 Days' => $q->whereBetween('date', [now()->subDays(29), now()]),
                    'This Month' => $q->whereBetween('date', [now()->startOfMonth(), now()->endOfMonth()]),
                    'Last Month' => $q->whereBetween('date', [now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth()]),
                    default => $q
                };
            })
            // ✅ For non-admin restrict by user
            ->when(request()->segment(1) !== 'admin' && auth()->user(),
                fn ($q) => $q->forUserTicketOption(auth()->user()->id)
            )
            // ✅ Order handling
            ->when($request->has('order'), function ($q) use ($request) {
                $columns = collect($this->getColumns())->pluck('name')->values()->all();
                $order = $request->get('order')[0];
                $columnIndex = $order['column'];
                $direction = $order['dir'];
                if (isset($columns[$columnIndex])) {
                    $q->orderBy($columns[$columnIndex], $direction);
                }
            });
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('draw-details-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(0, 'desc')
            ->selectStyleSingle()
            ->addTableClass('table table-bordered table-hover')
            ->setTableHeadClass('bg-warning text-white')
            ->parameters([
                'ordering' => true,
                'searching' => true,
                'language' => ['searchPlaceholder' => 'Enter Hour Or Minute'],
            ])
            ->buttons([
                Button::make('excel'),
                Button::make('csv'),
                Button::make('pdf'),
                Button::make('print'),
            ]);
    }

    public function getColumns(): array
    {
        $columns = [
            Column::make('updated_at')->hidden(),
            Column::make('end_time')->title('Time')->orderable(true)->searchable(true),
            Column::make('tq')->title('TQ'),
            Column::make('claim'),
            Column::make('cross_amt')->title('Cross Amt.'),
            Column::make('cross_claim')->title('Cross Claim'),
            Column::make('p_and_l')->title('P&L'),
            Column::make('created_at'),
        ];

        if (request()->segment(1) === 'admin') {
            $columns[] = Column::make('action')->orderable(false);
        }

        return $columns;
    }

    protected function filename(): string
    {
        return 'Shopkeepers_'.date('YmdHis');
    }
}
