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
                $url = route('admin.draw.details.shopkeeper', ['drawDetail' => $user_draw->draw_detail_id, 'user' => $user_draw->user_id]);
                $shopkeeper = $user_draw->user->name;

                return "<a href='$url' class='text-primary'>$shopkeeper</a>";

            })
            ->addColumn('tq', function ($user_draw) {
                $ticket_option = $user_draw->ticketOptions;
                $total_qty = $ticket_option->sum('a_qty') + $ticket_option->sum('b_qty') + $ticket_option->sum('c_qty');

                return $total_qty;
            })
            ->addColumn('t_amt', function ($user_draw) {
                $ticket_option = $user_draw->ticketOptions;
                $t_amt = $ticket_option->sum('a_qty') + $ticket_option->sum('b_qty') + $ticket_option->sum('c_qty');

                return $t_amt * 100;
            })
            ->addColumn('claim', function ($user_draw) {
                $draw_details = $user_draw->drawDetail;
                $ticket_option = $user_draw->ticketOptions;
                $total_a_claim = $ticket_option->where('number', $draw_details->claim_a)->sum('a_qty');
                $total_b_claim = $ticket_option->where('number', $draw_details->claim_b)->sum('b_qty');
                $total_c_claim = $ticket_option->where('number', $draw_details->claim_c)->sum('c_qty');
                $total_claim = $total_a_claim + $total_b_claim + $total_c_claim;

                return $total_claim;
            })
            ->addColumn('c_amt', function ($user_draw) {
                $draw_details = $user_draw->drawDetail;
                $ticket_option = $user_draw->ticketOptions;
                $total_a_claim = $ticket_option->where('number', $draw_details->claim_a)->sum('a_qty');
                $total_b_claim = $ticket_option->where('number', $draw_details->claim_b)->sum('b_qty');
                $total_c_claim = $ticket_option->where('number', $draw_details->claim_c)->sum('c_qty');
                $total_claim = $total_a_claim + $total_b_claim + $total_c_claim;

                return $total_claim * 100;
            })
            ->addColumn('p_and_l', function ($user_draw) {

                $ticket_option = $user_draw->ticketOptions;
                $total_amount = ($ticket_option->sum('a_qty') + $ticket_option->sum('b_qty') + $ticket_option->sum('c_qty')) * 100;

                $draw_details = $user_draw->drawDetail;
                $ticket_option = $user_draw->ticketOptions;
                $total_a_claim = $ticket_option->where('number', $draw_details->claim_a)->sum('a_qty');
                $total_b_claim = $ticket_option->where('number', $draw_details->claim_b)->sum('b_qty');
                $total_c_claim = $ticket_option->where('number', $draw_details->claim_c)->sum('c_qty');
                $c_amt = ($total_a_claim + $total_b_claim + $total_c_claim) * 100;

                $p_and_l = $total_amount - $c_amt;

                $bgClass = $p_and_l < 0 ? 'bg-danger text-white' : 'bg-success text-white';
                if ($p_and_l == 0) {
                    $bgClass = 'text-dark';
                }

                return <<<HTML
                <div class="{$bgClass}  text-center">{$p_and_l}</div>
                HTML;

                return 0;
            })
            ->rawColumns([
                'action',
                'tq', 't_amt', 'claim',
                'c_amt', 'p_and_l', 'shop_keeper',
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
