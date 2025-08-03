<?php

namespace App\DataTables;

use App\Models\Shopkeeper;
use App\Models\TicketOption;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Http\Request;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class NumberListDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param  QueryBuilder<Shopkeeper>  $query  Results from query() method.
     */
    public function dataTable(QueryBuilder $query, Request $request): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('ticket_number', function ($ticket_option) {
                return $ticket_option->ticket->ticket_number;
            })
            ->addColumn('total_collection_of_a', fn ($row) => $row->totalCollection($row->a_qty))
            ->addColumn('total_collection_of_b', fn ($row) => $row->totalCollection($row->b_qty))
            ->addColumn('total_collection_of_c', fn ($row) => $row->totalCollection($row->c_qty))
            ->addColumn('total_distribution_of_a', fn ($row) => $row->totalDistributions($row->a_qty))
            ->addColumn('total_distribution_of_b', fn ($row) => $row->totalDistributions($row->b_qty))
            ->addColumn('total_distribution_of_c', fn ($row) => $row->totalDistributions($row->c_qty))
            ->addColumn('numbers', fn ($row) => $row->number)
            ->addColumn('action', fn ($row) => '<a href="#" class="btn btn-primary">Details</a>')
            ->setRowId('number')
            ->editColumn('numbers', function ($row) {
                return "<a href='$row->number'>$row->number</a>";
            })
            ->rawColumns([
                'ticket_number',
                'action',
                'numbers',
                'total_collection_of_a',
                'total_collection_of_b',
                'total_collection_of_c',
                'total_distribution_of_a',
                'total_distribution_of_b',
                'total_distribution_of_c',
            ]);
    }

    /**
     * Get the query source of dataTable.
     *
     * @return QueryBuilder<Shopkeeper>
     */
    public function query(TicketOption $model, Request $request): QueryBuilder
    {

        return $model->newQuery()->forUser(auth()->user()->id)->where('number', $request->number);

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
            ->orderBy(1)
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
            // Column::make('id')->title('#ID')->hidden(),
            // Column::make('ticket_number')->title('Ticket No.'),
            Column::make('ticket_number')->title('Ticket Number'),
            // Column::make('numbers')->title('Number(0-9)'),
            Column::make('total_collection_of_a')->title('TTL. Coll. Of A'),
            Column::make('total_distribution_of_a')->title('TTL.  Dist. Of A'),
            Column::make('total_collection_of_b')->title('TTL. Coll. Of B'),
            Column::make('total_distribution_of_b')->title('TTL.  Dist. Of B'),
            Column::make('total_collection_of_c')->title('TTL. Coll. Of C'),
            Column::make('total_distribution_of_c')->title('TTL.  Dist. Of C'),
            // Column::make('action'),

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
