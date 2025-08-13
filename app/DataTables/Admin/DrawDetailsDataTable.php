<?php

namespace App\DataTables\Admin;

use App\Models\Shopkeeper;
use App\Models\UserDraw;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Http\Request;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class DrawDetailsDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param  QueryBuilder<Shopkeeper>  $query  Results from query() method.
     */
    public function dataTable(QueryBuilder $query, Request $request): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('shop_keeper', function ($user_draw) {
                return $user_draw->user->name;
            })
            ->addColumn('tq', function ($user_draw) {
                $ticket_option = $user_draw->ticketOptions;
                $total_qty = $ticket_option->sum('a_qty') + $ticket_option->sum('b_qty') + $ticket_option->sum('c_qty');

                return $total_qty;
            })
            ->addColumn('t_amt', function ($user_draw) {
                $ticket_option = $user_draw->ticketOptions;
                $total_qty = $ticket_option->sum('a_qty') + $ticket_option->sum('b_qty') + $ticket_option->sum('c_qty');

                return $total_qty * 100;
            })
            ->addColumn('claim', function ($ticket_option) {
                return 0;
            })
            ->addColumn('c_amt', function ($ticket_option) {
                return 0;
            })
            ->addColumn('p_and_l', function ($ticket_option) {
                return 0;
            })
            ->rawColumns([
                'action',
                'tq', 't_amt', 'claim',
                'c_amt', 'p_and_l',
            ]);
    }

    /**
     * Get the query source of dataTable.
     *
     * @return QueryBuilder<Shopkeeper>
     */
    public function query(UserDraw $model, Request $request): QueryBuilder
    {

        return $model->newQuery()->where('draw_detail_id', $request->drawDetail->id);
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
                        'searchPlaceholder' => 'Number(0-9)',
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
            Column::make('shop_keeper')->title('Shopkeeper'),
            Column::make('tq')->title('TQ'),
            Column::make('t_amt')->title('T Amt'),
            Column::make('claim')->title('Claim'),
            Column::make('c_amt')->title('C Amt.'),
            Column::make('p_and_l')->title('P&L'),

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
