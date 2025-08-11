<?php

namespace App\DataTables;

use App\Models\Shopkeeper;
use App\Models\TicketOption;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
                    $q->whereRaw("TIME_FORMAT(draws.end_time, '%h %i %p') LIKE ?", ["%{$keyword}%"])
                      // Match 12-hour without leading zero
                        ->orWhereRaw("TIME_FORMAT(draws.end_time, '%l %p') LIKE ?", ["%{$keyword}%"])
                      // Match 24-hour format
                        ->orWhereRaw("TIME_FORMAT(draws.end_time, '%H') LIKE ?", ["%{$keyword}%"]);
                });
            })

            ->addColumn('total_tickets', function ($row) {
                return $row->total_a_qty + $row->total_b_qty + $row->total_c_qty;
            })
            ->addColumn('t_amt', function ($row) {
                return ($row->total_a_qty + $row->total_b_qty + $row->total_c_qty) * 100;
            })
            ->addColumn('claim', function ($draw) {
                return 'N/A';
            })
            ->addColumn('c_amt', function ($draw) {
                return '';
            })
            ->addColumn('p_and_l', function ($draw) {
                return '';
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
                'total_tickets',
                'c_amt',
                'claim',
                'p_and_l', 't_amt']);
    }

    /**
     * Get the query source of dataTable.
     *
     * @return QueryBuilder<Shopkeeper>
     */
    public function query(TicketOption $model, Request $request): QueryBuilder
    {

        DB::enableQueryLog();
        $ticket_options = $model->newQuery()
            ->select(
                'ticket_options.draw_id',
                'draws.end_time',
                DB::raw('SUM(ticket_options.a_qty) as total_a_qty'),
                DB::raw('SUM(ticket_options.b_qty) as total_b_qty'),
                DB::raw('SUM(ticket_options.c_qty) as total_c_qty')
            )
            ->join('draws', 'ticket_options.draw_id', '=', 'draws.id')
            ->when(! auth()->guard('admin')->check() && auth()->user(), function ($q) {
                return $q->forUser(auth()->user()->id);
            })
            // keep if you want user filtering
            ->when($request->get('start_date'), function ($query) use ($request) {
                $query->whereDate('ticket_options.created_at', $request->get('start_date'));
            })
            ->groupBy('ticket_options.draw_id', 'draws.end_time')
            ->orderBy('draws.end_time', 'desc');

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
            Column::make('total_tickets')->title('TQ'),
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
