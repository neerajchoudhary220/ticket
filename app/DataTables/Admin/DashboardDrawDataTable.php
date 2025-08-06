<?php

namespace App\DataTables\Admin;

use App\Models\Draw;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Http\Request;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class DashboardDrawDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query, Request $request): EloquentDataTable
    {

        return (new EloquentDataTable($query))
            // ->addColumn('action', function ($draw) {
            //     return view('admin.draw.draw-action', compact('draw'))->render();
            // })
            ->editColumn('id', function ($draw) {
                $draw_details_list = route('admin.draw.detail.list', ['draw_id' => $draw->id]);

                return "<a class='text-primary' href='$draw_details_list'>DN - {$draw->id}</a>";
            })

            ->editColumn('end_time', function ($draw) {
                return $draw->formatStartTime();
            })
            ->editColumn('total_collection', function ($draw) {
                return $draw->total_collection ?: 0;

            })
            ->editColumn('total_rewards', function ($draw) {
                return $draw->total_rewards ?: 0;

            })
            ->addColumn('claim', function ($ticket_option) {
                return '0';
            })
            ->addColumn('camt', function ($ticket_option) {
                return '0';
            })
            ->addColumn('pl', function ($ticket_option) {
                return '0';
            })
            ->setRowId('id')
            ->rawColumns(['id', 'claim', 'camt', 'pl']);
    }

    /**
     * Get the query source of dataTable.
     *
     * @return QueryBuilder<Shopkeeper>
     */
    public function query(Draw $model): QueryBuilder
    {
        return $model->newQuery();
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
            ->addTableClass('table  custom-header')
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
            // Column::make('id')->title('#Draw No.'),
            Column::make('end_time')->title('Time')->width(100),
            Column::make('total_collection')->title('TQ'),
            Column::make('total_rewards')->title('T Amt'),
            Column::make('claim')->title('Claim'),
            Column::make('camt')->title('Camt'),
            Column::make('pl')->title('P&L'),

        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'draw_'.date('YmdHis');
    }
}
