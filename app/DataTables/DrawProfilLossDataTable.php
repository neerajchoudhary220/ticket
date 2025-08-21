<?php

namespace App\DataTables;

use App\Models\DrawDetail;
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
     * @param  QueryBuilder<DrawDetail>  $query
     */
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

            ->editColumn('tq', function ($row) {
                $url = request()->segment(1) === 'admin'
                    ? route('admin.dashboard.total.qty.details.list', $row->id)
                    : route('dashboard.draw.total.qty.list.details', $row->id);

                return "<a href='$url' class='text-primary h6'>{$row->tq}</a>";
            })
            ->editColumn('cross_amt', function ($row) {
                $url = request()->segment(1) === 'admin'
                    ? route('admin.dashboard.cross.abc', ['draw_detail_id' => $row->id])
                    : route('dashboard.draw.cross.abc.details.list', ['draw_detail_id' => $row->id]);

                return "<a href='$url' class='text-primary h6'>{$row->cross_amt}</a>";
            })
            ->editColumn('p_and_l', function ($row) {
                $p_and_l = (int) $row->p_and_l;
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
            ->rawColumns(['end_time', 'tq', 'cross_amt', 'p_and_l', 'action', 'cross_claim']);
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
