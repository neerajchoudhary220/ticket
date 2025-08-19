<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\CrossAbcDataTable;
use App\DataTables\DrawProfilLossDataTable;
use App\Http\Controllers\Controller;
use App\Models\DrawDetail;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index(DrawProfilLossDataTable $dataTable)
    {
        $data = [
            'total_shopkeepers' => User::count(),
            'total_tickets' => Ticket::count(),
            'total_claims' => DrawDetail::sum('claim'),
            'total_cross_claim' => DrawDetail::sum('total_cross_amt'),
            'claimed' => DrawDetail::where('date', Carbon::today())
                ->where('claim', '!=', 0)
                ->count(),

        ];

        // return view('admin.dashboard.index', compact('data'));
        return $dataTable->render('admin.dashboard.index', compact('data'));
    }

    public function crossAbc(CrossAbcDataTable $dataTable, Request $request)
    {
        $drawDetail = DrawDetail::findOrFail($request->get('draw_detail_id'));
        $type = $request->get('type') ?? 'AB';

        return $dataTable->render('admin.dashboard.cross-abc-details', compact('drawDetail', 'type'));
        // return view('admin.dashboard.cross-abc-details')
    }

    public function totalQtyDetailList(DrawDetail $drawDetail)
    {

        return view('admin.dashboard.total-qty-details-table', compact('drawDetail'));
    }
}
