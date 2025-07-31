<?php

namespace App\DataTables;

use App\Models\Draw;
use App\Models\Shopkeeper;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Http\Request;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class UserDrawDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param  QueryBuilder<Shopkeeper>  $query  Results from query() method.
     */
    public function dataTable(QueryBuilder $query, Request $request): EloquentDataTable
    {

        $auth_user_id = $request->user()->id;
        $query->forUser($auth_user_id);

        return (new EloquentDataTable($query))
            ->editColumn('id', function ($draw) {
                return "<span>DN - {$draw->id}</span>";
            })
            ->editColumn('end_time', function ($draw) {
                return $draw->formatEndTime();

            })
            ->editColumn('start_time', function ($draw) {
                return $draw->formatStartTime();
            })
            ->addColumn('total_tickets', function ($draw) use ($auth_user_id) {
                return $draw->tickets()->forUser($auth_user_id)->count();
            })
            ->addColumn('total_completed_tickets', function ($draw) use ($auth_user_id) {
                return $draw->tickets()->forUser($auth_user_id)->completed()->count();
            })
            ->addColumn('total_running_tickets', function ($draw) use ($auth_user_id) {
                return $draw->tickets()->forUser($auth_user_id)->running()->count();
            })
            ->addColumn('action', function ($draw) {
                $url = route('dashboard.option.list', ['draw_id' => $draw->id]);
                $draw_details = route('dashboard.draw.details.list', ['draw_id' => $draw->id]);

                return <<<HTML
        <a href="{$url}" class="btn btn-primary">Details</a>
        <a href="{$draw_details}" class ="btn btn-secondary">Distribution & Collections</a>
    HTML;
            })

            ->setRowId('id')
            ->rawColumns(['action', 'id', 'total_completed_tickets', 'total_running_tickets', 'total_tickets']);
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
            // Column::computed('action')
            //     ->exportable(false)
            //     ->printable(false)
            //     ->width(60)
            //     ->addClass('text-center'),
            Column::make('id')->title('#ID'),
            Column::make('start_time')->title('Start Time'),
            Column::make('end_time')->title('End Time'),
            Column::make('total_completed_tickets')->title('Total Completed Tickets'),
            Column::make('total_running_tickets')->title('Total Running Tickets'),
            Column::make('total_tickets')->title('Total Tickets'),

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
