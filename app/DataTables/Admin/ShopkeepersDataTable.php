<?php

namespace App\DataTables\Admin;

use App\Models\Shopkeeper;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Http\Request;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class ShopkeepersDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param  QueryBuilder<Shopkeeper>  $query  Results from query() method.
     */
    public function dataTable(QueryBuilder $query, Request $request): EloquentDataTable
    {
        // $searchValue = $request->input('search.value');
        // $searchColumn = $request->input('data');
        // $query->when($searchColumn === 'name' && $searchValue, function ($q) use ($searchValue) {
        //     $q->forName($searchValue);
        // });

        return (new EloquentDataTable($query))
            ->filterColumn('name', function ($query, $keyword) {
                return $query->forName($keyword);
            })
            ->addColumn('action', function () {
                // $shopkeeprEditUrl = route('ticket.add', $ticket->id);

                return <<<'HTML'
                <div class="d-flex justify-content-center">
                <!-- <a href="#" class="btn btn-secondary"><i class="fa fa-eye"></i> View Details</a> -->
                <a href="#" class="btn btn-warning ms-3 text-white"><i class="fa fa-pencil"></i> Edit</a>
                </div>
                HTML;
            })
            ->setRowId('id')
            ->rawColumns(['action']);
    }

    /**
     * Get the query source of dataTable.
     *
     * @return QueryBuilder<Shopkeeper>
     */
    public function query(User $model): QueryBuilder
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
            Column::make('name')->title('Name'),
            Column::make('email'),
            Column::make('mobile_number'),
            Column::make('created_at'),
            Column::make('updated_at'),
            Column::make('action')->addClass('text-center'),

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
