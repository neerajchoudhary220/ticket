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
        $user = auth()->user();

        $totalAbCrossAmt = $user->crossAbcDetail()
            ->where('draw_detail_id', $drawDetail->id)
            ->where('type', 'AB')->sum('amount');
        $totalBcCrossAmt = $user->crossAbcDetail()
            ->where('draw_detail_id', $drawDetail->id)
            ->where('type', 'BC')->sum('amount');

        $totalAcCrossAmt = $user->crossAbcDetail()
            ->where('draw_detail_id', $drawDetail->id)
            ->where('type', 'AC')->sum('amount');

        $totalAbCrossClaimAmt = $user->crossAbcDetail()
            ->where('draw_detail_id', $drawDetail->id)
            ->where('number', $drawDetail->ab)
            ->where('type', 'AB')->sum('amount');
        $totalBcCrossClaimAmt = $user->crossAbcDetail()
            ->where('draw_detail_id', $drawDetail->id)
            ->where('number', $drawDetail->bc)
            ->where('type', 'BC')->sum('amount');

        $totalAcCrossClaimAmt = $user->crossAbcDetail()
            ->where('draw_detail_id', $drawDetail->id)
            ->where('number', $drawDetail->ac)
            ->where('type', 'AC')->sum('amount');

        $ab_pl = $totalAbCrossAmt - ($totalAbCrossClaimAmt * 100);
        $ac_pl = $totalAcCrossAmt - ($totalAcCrossClaimAmt * 100);
        $bc_pl = $totalBcCrossAmt - ($totalBcCrossClaimAmt * 100);

        return $dataTable->render('web.dashboard.abc-cross-detail-list', [
            'drawDetail' => $drawDetail,
            'totalAbCrossAmt' => $totalAbCrossAmt,
            'totalBcCrossAmt' => $totalBcCrossAmt,
            'totalAcCrossAmt' => $totalAcCrossAmt,

            'totalAbCrossClaimAmt' => $totalAbCrossClaimAmt,
            'totalBcCrossClaimAmt' => $totalBcCrossClaimAmt,
            'totalAcCrossClaimAmt' => $totalAcCrossClaimAmt,

            'ab_pl' => $ab_pl,
            'ac_pl' => $ac_pl,
            'bc_pl' => $bc_pl,

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
