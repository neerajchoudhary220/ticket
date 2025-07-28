<?php

namespace App\DataTables;

use App\Models\Options;
use App\Models\Shopkeeper;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Http\Request;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class OptionDataTable extends DataTable
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
        $query->where('user_id', $request->user()->id);

        return (new EloquentDataTable($query))
            ->addColumn('ticket_no', function ($query) {
                return $query->ticket->ticket_number;
            })
            ->addColumn('draw_no', function ($query) {
                return $query->draw->id;
            })
            ->addColumn('action', function () {
                return view('admin.shopkeepers.shopkeeper-action')->render();
            })
            ->setRowId('id')
            ->rawColumns(['action']);
    }

    /**
     * Get the query source of dataTable.
     *
     * @return QueryBuilder<Shopkeeper>
     */
    public function query(Options $model): QueryBuilder
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
            Column::make('ticket_no')->title('Ticket No.'),
            Column::make('draw_no')->title('Draw No.'),
            Column::make('option'),
            Column::make('number'),
            Column::make('qty'),
            Column::make('total'),
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
