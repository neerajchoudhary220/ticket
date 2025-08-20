<?php

namespace App\Http\Controllers;

use App\DataTables\Admin\ShopKeeperDrawDetailsDataTable;
use App\DataTables\CrossAbDataTable;
use App\DataTables\CrossAcDataTable;
use App\DataTables\CrossBcDataTable;
use App\DataTables\DrawProfilLossDataTable;
use App\DataTables\TicketDetailsDataTable;
use App\Models\Draw;
use App\Models\DrawDetail;
use App\Models\Ticket;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(DrawProfilLossDataTable $dataTable)
    {
        // return view('web.dashboard.index');

        return $dataTable->render('web.dashboard.index');

    }

    public function addTicket(Request $request)
    {
        $ticket = null;
        $number = $request->number ?: null;

        if ($request->ticket_id) {
            $ticket = Ticket::where('id', $request->ticket_id)
                ->where('user_id', $request->user()->id)
                ->firstOrFail();
        }

        return view('web.ticket.add-ticket', compact('ticket', 'number'));
    }

    public function optionList(TicketDetailsDataTable $dataTable, Request $request)
    {
        $draw = Draw::findOrFail($request->draw_id);

        return $dataTable->render('web.dashboard.option-list', compact('draw'));
    }

    public function drawDetailsList(ShopKeeperDrawDetailsDataTable $dataTable, Request $request)
    {
        $drawDetail = DrawDetail::findOrFail($request->draw_detail_id);

        return $dataTable->render('web.dashboard.draw-details-datatable', compact('drawDetail'));

    }

    public function totalQtyDetailList(DrawDetail $drawDetail)
    {
        return view('web.dashboard.total-qty-list-detail', compact('drawDetail'));
    }

    public function crossAbcList(CrossAbDataTable $dataTable, CrossAcDataTable $crossAcDataTable, CrossBcDataTable $crossBcDataTable, DrawDetail $drawDetail, Request $request)
    {
        $drawDetail = DrawDetail::findOrFail($request->get('draw_detail_id'));

        return $dataTable->render('web.dashboard.abc-cross-detail-list', [
            'drawDetail' => $drawDetail,
            'crossAcDataTable' => $crossAcDataTable->html(),
            'crossBcDataTable' => $crossBcDataTable->html(),
        ]);
        // return view('web.dashboard.abc-cross-detail-list');

    }

    public function getCrossAcList(CrossAcDataTable $crossAcDataTable, CrossBcDataTable $crossBcDataTable)
    {
        return $crossAcDataTable->render('web.dashboard.abc-cross-detail-list', compact('crossBcDataTable'));
    }

    public function getCrossBcList(CrossAcDataTable $crossAcDataTable, CrossBcDataTable $crossBcDataTable)
    {
        return $crossBcDataTable->render('web.dashboard.abc-cross-detail-list', compact('crossAcDataTable'));
    }
}
