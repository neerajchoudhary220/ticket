<?php

namespace App\DataTables\Admin;

use App\Models\Shopkeeper;
use App\Models\TicketOption;
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
        return (new EloquentDataTable($query))
            ->editColumn('number', function ($ticket_option) {
                return $ticket_option->number;
            })
            ->filterColumn('number', function ($query, $keyword) {
                $query->whereRaw('number like ?', ['%'.strtolower($keyword).'%']);
            })
            ->addColumn('total_collection_of_a', fn ($row) => $row->totalCollection($row->a_qty))
            ->addColumn('total_collection_of_b', fn ($row) => $row->totalCollection($row->b_qty))
            ->addColumn('total_collection_of_c', fn ($row) => $row->totalCollection($row->c_qty))
            ->addColumn('total_distribution_of_a', fn ($row) => $row->totalDistributions($row->a_qty))
            ->addColumn('total_distribution_of_b', fn ($row) => $row->totalDistributions($row->b_qty))
            ->addColumn('total_distribution_of_c', fn ($row) => $row->totalDistributions($row->c_qty))
            // ->addColumn('shopkeeper', function ($row) {
            //     return "<a href='#'>{$row->user->name}</a>";
            // })
            // ->setRowId('number')
            // ->editColumn('number', function ($row) {
            //     return "<a href='$row->number'>$row->number</a>";
            // })
            // ->addColumn('action', function ($ticket_option) {
            //     $add_ticket_url = '#';

            //     return <<<HTML
            //     <div class="d-flex justify-content-center">
            //     <a href="$add_ticket_url" class="btn btn-primary btn-sm ms-3 text-white">More Details <i class="fa fa-arrow-circle-right"></i></a>

            //     </div>
            //     HTML;
            // })
            ->rawColumns([
                'action',
                'number',
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

        return $model->newQuery()
            ->forTicket($request->ticket_id);

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
            ->parameters([
                'searching' => true,
                'language' => [
                    'searchPlaceholder' => 'Number(0-9)',
                ],
            ])
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
            Column::make('number')->title('Number(0-9)')->orderable(true)->searchable(true),
            Column::make('total_collection_of_a')->title('TTL. Coll. Of A'),
            Column::make('total_distribution_of_a')->title('TTL.  Dist. Of A'),
            Column::make('total_collection_of_b')->title('TTL. Coll. Of B'),
            Column::make('total_distribution_of_b')->title('TTL.  Dist. Of B'),
            Column::make('total_collection_of_c')->title('TTL. Coll. Of C'),
            Column::make('total_distribution_of_c')->title('TTL.  Dist. Of C'),
            // Column::make('shopkeeper')->title('Shopkeeper'),
            // Column::make('action')->addClass('text-center'),

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
