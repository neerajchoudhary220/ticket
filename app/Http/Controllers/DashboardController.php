<?php

namespace App\Http\Controllers;

use App\DataTables\TicketDetailsDataTable;
use App\DataTables\UserDrawDataTable;
use App\DataTables\UserDrawDetailsDataTable;
use App\Models\Draw;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(UserDrawDataTable $dataTable)
    {
        // return view('web.dashboard.index');

        return $dataTable->render('web.dashboard.index');

    }

    public function optionList(TicketDetailsDataTable $dataTable, Request $request)
    {
        $draw = Draw::findOrFail($request->draw_id);

        return $dataTable->render('web.dashboard.option-list', compact('draw'));
    }

    public function drawDetailsList(UserDrawDetailsDataTable $dataTable, Request $request)
    {
        // return view('web.dashboard.draw-details-datatable');
        $draw = Draw::findOrFail($request->draw_id);

        return $dataTable->render('web.dashboard.draw-details-datatable', compact('draw'));

    }
}
