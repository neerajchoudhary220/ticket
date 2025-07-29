<?php

namespace App\Http\Controllers;

use App\DataTables\TicketDetailsDataTable;
use App\DataTables\UserDrawDataTable;
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
        $draw = Draw::find($request->draw_id);

        return $dataTable->render('web.dashboard.option-list', compact('draw'));
    }
}
