<?php

namespace App\DataTables;

use App\Models\DrawDetail;
use App\Models\Shopkeeper;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Http\Request;
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
            ->editColumn('end_time', function ($row) {
                return \Carbon\Carbon::parse($row->end_time)->format('h:i a');
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
                return $row->total_qty ?? 0;
            })
            ->addColumn('t_amt', function ($row) {
                return $row->total_qty ? ($row->total_qty * 100) : 0;
            })
            ->addColumn('claim', function ($draw) {
                return 'N/A';
            })
            ->addColumn('c_amt', function ($draw) {
                return 'N/A';
            })
            ->addColumn('p_and_l', function ($draw) {
                return 'N/A';
            })
            ->addColumn('action', function ($draw) {
                $draw_details = route('dashboard.draw.details.list', ['draw_id' => $draw->id]);

                return <<<HTML
        <a href="{$draw_details}" class ="btn btn-primary">Details</a>
    HTML;
            })

            ->setRowId('id')
            ->rawColumns(['action',
                'id',
                'end_time',
                'tq',
                'c_amt',
                'claim',
                'p_and_l', 't_amt']);
    }

    /**
     * Get the query source of dataTable.
     *
     * @return QueryBuilder<Shopkeeper>
     */
    public function query(DrawDetail $model, Request $request): QueryBuilder
    {

        $ticket_options = $model->newQuery()
            ->when(! auth()->guard('admin')->check() && auth()->user(), function ($q) {
                return $q->forUser(auth()->user()->id);
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
            ->orderBy('end_time', 'desc');

        return $ticket_options;
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('shopkeepers-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(0)
            ->selectStyleSingle()
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
                Button::make('reset'),
                Button::make('reload'),
            ]);
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        return [
            Column::make('end_time')->title('Time')->orderable(true)->searchable(true),
            Column::make('tq')->title('TQ'),
            Column::make('t_amt')->title('T Amt'),
            Column::make('claim'),
            Column::make('c_amt')->title('C Amt.'),
            Column::make('p_and_l')->title('P&L'),
            Column::make('action'),

        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'Shopkeepers_'.date('YmdHis');
    }
}
