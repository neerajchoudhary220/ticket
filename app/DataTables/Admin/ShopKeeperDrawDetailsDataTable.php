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

class ShopKeeperDrawDetailsDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param  QueryBuilder<Shopkeeper>  $query  Results from query() method.
     */
    public function query(TicketOption $model, Request $request): QueryBuilder
    {
        return $model->newQuery()
            ->selectRaw('
            draw_detail_id,user_id,
            ticket_id,
            SUM(a_qty) as total_a_qty,
            SUM(b_qty) as total_b_qty,
            SUM(c_qty) as total_c_qty,
            SUM(CASE WHEN number = draw_details.claim_a THEN a_qty ELSE 0 END) as claim_a_qty,
            SUM(CASE WHEN number = draw_details.claim_b THEN b_qty ELSE 0 END) as claim_b_qty,
            SUM(CASE WHEN number = draw_details.claim_c THEN c_qty ELSE 0 END) as claim_c_qty
        ')
            ->join('draw_details', 'ticket_options.draw_detail_id', '=', 'draw_details.id')
            ->where('draw_detail_id', $request->drawDetail->id)
            ->where('user_id', $request->user->id)
            // ->groupBy('ticket_id', 'draw_details.claim_a', 'draw_details.claim_b', 'draw_details.claim_c')
            ->groupBy(
                'ticket_options.draw_detail_id',
                'ticket_options.user_id',
                'ticket_options.ticket_id',
                'draw_details.claim_a',
                'draw_details.claim_b',
                'draw_details.claim_c'
            )

            ->with('ticket'); // eager load ticket for ticket_number
    }

    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('ticket_no', function ($TicketOption) {
                $url = route('admin.draw.ticke.details.list', ['drawDetail' => $TicketOption->draw_detail_id, 'ticket' => $TicketOption->ticket_id, 'user' => $TicketOption->user_id]);

                return "<a href='$url' class='text-primary'>{$TicketOption->ticket->ticket_number}</a>";
            })
            ->addColumn('tq', function ($row) {
                return $row->total_a_qty + $row->total_b_qty + $row->total_c_qty;
            })
            ->addColumn('t_amt', function ($row) {
                $total_qty = $row->total_a_qty + $row->total_b_qty + $row->total_c_qty;

                return $total_qty * 100;
            })
            ->addColumn('claim', function ($row) {
                return $row->claim_a_qty + $row->claim_b_qty + $row->claim_c_qty;
            })
            ->addColumn('c_amt', function ($row) {
                $total_claim = $row->claim_a_qty + $row->claim_b_qty + $row->claim_c_qty;

                return $total_claim * 100;
            })
            ->addColumn('p_and_l', function ($row) {
                $total_amount = ($row->total_a_qty + $row->total_b_qty + $row->total_c_qty) * 100;
                $claim_amount = ($row->claim_a_qty + $row->claim_b_qty + $row->claim_c_qty) * 100;
                $p_and_l = $total_amount - $claim_amount;

                $bgClass = $p_and_l < 0 ? 'bg-danger text-white' : 'bg-success text-white';
                if ($p_and_l == 0) {
                    $bgClass = 'text-dark';
                }

                return <<<HTML
                <div class="{$bgClass} text-center">{$p_and_l}</div>
            HTML;
            })
            ->rawColumns(['ticket_no', 'tq', 't_amt', 'claim', 'c_amt', 'p_and_l']);
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
            Column::make('ticket_no')->title('Tno.'),
            Column::make('tq')->title('TQ'),
            Column::make('t_amt')->title('T amt.'),
            Column::make('claim')->title('Claim'),
            Column::make('c_amt')->title('C Amt'),
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
