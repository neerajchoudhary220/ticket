<?php

namespace App\DataTables;

use App\Models\DrawDetail;
use App\Models\Shopkeeper;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class DrawProfilLossDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param  QueryBuilder<Shopkeeper>  $query  Results from query() method.
     */
    public function dataTable(QueryBuilder $query, Request $request): EloquentDataTable
    {

        return (new EloquentDataTable($query))
            ->editColumn('end_time', function ($draw_detail) {
                $end_time = \Carbon\Carbon::parse($draw_detail->end_time)->format('h:i a');
                $url = request()->segment(1) === 'admin' ? route('admin.draw.detail.list', $draw_detail->id) : route('dashboard.draw.details.list', ['draw_detail_id' => $draw_detail->id]);

                return "<a href='$url' class='text-primary h6'>$end_time</a>";
            })
            ->filterColumn('end_time', function ($query, $keyword) {
                $keyword = strtolower($keyword);

                $query->where(function ($q) use ($keyword) {
                    // Match 12-hour with leading zero
                    $q->whereRaw("TIME_FORMAT(end_time, '%h %i %p') LIKE ?", ["%{$keyword}%"])
                      // Match 12-hour without leading zero
                        ->orWhereRaw("TIME_FORMAT(end_time, '%l %p') LIKE ?", ["%{$keyword}%"])
                      // Match 24-hour format
                        ->orWhereRaw("TIME_FORMAT(end_time, '%H') LIKE ?", ["%{$keyword}%"]);
                });
            })

            ->addColumn('tq', function ($row) {
                $total_qty_list_details_url = request()->segment(1) === 'admin' ? route('admin.dashboard.total.qty.details.list', $row->id) : route('dashboard.draw.total.qty.list.details', $row->id);
                $tq = $row->total_qty ?? 0;

                return "<a href='$total_qty_list_details_url' class='text-primary h6'>$tq</a>";

            })
            ->addColumn('t_amt', function ($row) {
                return $row->total_qty ? ($row->total_qty * 11) : 0;
            })
            ->addColumn('claim', function ($draw_detail) {
                return $draw_detail->claim ?? 0;
            })
            ->addColumn('c_amt', function ($draw_detail) {
                return $draw_detail->claim ? (int) $draw_detail->claim * 100 : 0;
            })
            ->addColumn('cross_amt', function ($draw_detail) {
                $abc_cross_url = request()->segment(1) === 'admin' ? route('admin.dashboard.cross.abc', ['draw_detail_id' => $draw_detail->id]) : route('dashboard.draw.cross.abc.details.list', ['draw_detail_id' => $draw_detail->id]);
                $total_cross_amt = $draw_detail->total_cross_amt ?? 0;

                return "<a href='$abc_cross_url'  class='text-primary h6'>$total_cross_amt</a>";
            })
            ->addColumn('cross_claim', function ($draw_detail) {
                return (int) $draw_detail->claim_ab + (int) $draw_detail->claim_ac + (int) $draw_detail->claim_bc;
            })
            ->addColumn('p_and_l', function ($draw_detail) {
                $tq_11 = $draw_detail->total_qty ? ((int) $draw_detail->total_qty * 11) : 0;
                $claim_q_100 = $draw_detail->claim ? (int) $draw_detail->claim * 100 : 0;
                $cross_amt = (int) $draw_detail->total_cross_amt ?? 0;
                $cross_claim_100 = ((int) $draw_detail->claim_ab + (int) $draw_detail->claim_ac + (int) $draw_detail->claim_bc) * 100;
                $p_and_l = ($tq_11 - $claim_q_100) + $cross_amt - $cross_claim_100;
                $bgClass = $p_and_l < 0 ? 'bg-danger text-white' : 'bg-success text-white';
                if ($p_and_l == 0) {
                    $bgClass = 'text-dark';
                }

                return <<<HTML
                <div class="{$bgClass}  text-center">{$p_and_l}</div>
                HTML;

            })
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
            ->editColumn('created_at', function ($draw_detail) {
                return Carbon::parse($draw_detail->created_at)->format('Y-m-d');
            })

            ->setRowId('id')
            ->rawColumns(['action',
                'id',
                'end_time',
                'tq',
                'c_amt',
                'claim', 'cross_amt', 'cross_claim',
                'p_and_l', 't_amt', 'created_at']);

    }

    /**
     * Get the query source of dataTable.
     *
     * @return QueryBuilder<Shopkeeper>
     */
    public function query(DrawDetail $model, Request $request): QueryBuilder
    {

        $ticket_options = $model->newQuery()
            // ->whereDate('date', Carbon::today())
            ->when(! $request->get('start_date') && ! $request->get('end_date') && ! $request->get('da7'), function ($query) {
                $query->whereDate('date', now());
            })
            ->when($request->filled('start_date') && $request->filled('end_date'), function ($query) use ($request) {
                // Filter between range
                $query->whereBetween('date', [
                    $request->get('start_date'),
                    $request->get('end_date'),
                ]);
            })
            ->when($request->filled('start_date') && ! $request->filled('end_date'), function ($query) use ($request) {
                // Only start_date provided
                $query->whereDate('date', $request->get('start_date'));
            })
            ->when($request->filled('day'), function ($query) use ($request) {
                // Optional: if "day" param from predefined ranges exists, handle it here
                $day = $request->get('day');
                if ($day === 'Today') {
                    $query->whereDate('date', now());
                } elseif ($day === 'Yesterday') {
                    $query->whereDate('date', now()->subDay());
                } elseif ($day === 'Last 7 Days') {
                    $query->whereBetween('date', [now()->subDays(6), now()]);
                } elseif ($day === 'Last 30 Days') {
                    $query->whereBetween('date', [now()->subDays(29), now()]);
                } elseif ($day === 'This Month') {
                    $query->whereBetween('date', [now()->startOfMonth(), now()->endOfMonth()]);
                } elseif ($day === 'Last Month') {
                    $query->whereBetween('date', [now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth()]);
                }
            })
            ->when(request()->segment(1) !== 'admin' && auth()->user(), function ($q) {
                return $q->forUserTicketOption(auth()->user()->id);
            })
            ->orderBy('claim', 'desc')
            ->orderBy('end_time', 'desc');
        // ->when(! $request->has('order'), function ($query) {
        //     $query->orderBy('end_time', 'asc');
        // });

        return $ticket_options;
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('draw-details-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(0, 'desc')
            ->selectStyleSingle()
            ->addTableClass('table table-bordered  table-hover')
            ->setTableHeadClass('bg-warning text-white')
            ->parameters(
                [
                    'searching' => true,
                    'language' => [
                        'searchPlaceholder' => 'Enter Hour Or Minute',
                    ],
                ]
            )
            ->buttons([
                Button::make('excel'),
                Button::make('csv'),
                Button::make('pdf'),
                Button::make('print'),
                // Button::make('reset'),
                // Button::make('reload'),
            ]);
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {

        $columes = [
            Column::make('updated_at')->hidden(),
            Column::make('end_time')->title('Time')->orderable(true)->searchable(true),
            Column::make('tq')->title('TQ')->orderable(true),
            // Column::make('t_amt')->title('T Amt'),
            Column::make('claim')->orderable(true),
            // Column::make('c_amt')->title('C Amt.'),
            Column::make('cross_amt')->title('Cross Amt.'),
            Column::make('cross_claim')->title('Cross Claim'),

            Column::make('p_and_l')->title('P&L'),
            Column::make('created_at'),
            // Column::make('action')->addClass('text-center'),

        ];
        if (request()->segment(1) === 'admin') {

            $columes[] = Column::make('action');
        }

        return $columes;
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'Shopkeepers_'.date('YmdHis');
    }
}
