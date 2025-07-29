<?php

namespace App\DataTables;

use App\Models\Shopkeeper;
use App\Models\Ticket;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Http\Request;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class TicketDetailsDataTable extends DataTable
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
        $query->forUser($request->user()->id)->where('draw_id', $request->draw_id);

        return (new EloquentDataTable($query))
            ->addColumn('draw_id', function ($ticket) {
                $draw_id = $ticket->draw_id;

                return <<<HTML
                <span>{$draw_id}</span>
                HTML;
            })
            ->addColumn('status', function ($ticket) {
                $status = $ticket->status;
                if ($status === 'COMPLETED') {
                    return <<<'HTML'
                    <div class="d-flex justify-content-center">
                   <div class="bg-success text-white p-0 w-50 text-center">Completed</div>
                </div>
                HTML;
                }

                return <<<'HTML'
                    <div class="d-flex justify-content-center">
                   <div class="bg-warning text-white p-0 w-50 text-center">Running</div>
                </div>
                HTML;

            })
            ->addColumn('action', function () {
                return <<<'HTML'
                <div class="d-flex justify-content-center">
                <!-- <a href="#" class="btn btn-secondary"><i class="fa fa-eye"></i> View Details</a> -->
                <a href="#" class="btn btn-warning ms-3 text-white"><i class="fa fa-pencil"></i> Edit</a>
                </div>
                HTML;
            })
            ->setRowId('id')
            ->rawColumns(['action', 'draw_id', 'status']);
    }

    /**
     * Get the query source of dataTable.
     *
     * @return QueryBuilder<Shopkeeper>
     */
    public function query(Ticket $model): QueryBuilder
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
            Column::make('full_ticket_no')->title('Ticket No.'),
            Column::make('draw_id')->title('Draw No.'),
            Column::make('status')->addClass('text-center'),
            // Column::make('option'),
            // Column::make('number'),
            // Column::make('total'),
            // Column::make('Total of A'),
            // Column::make('Total of B'),
            // Column::make('Total of C'),
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
