<?php

namespace App\Http\Controllers;

use App\DataTables\DrawDataTable;
use App\DataTables\OptionDataTable;

class DashboardController extends Controller
{
    public function index(DrawDataTable $dataTable)
    {
        // return view('web.dashboard.index');

        return $dataTable->render('web.dashboard.index');

    }

    public function optionList(OptionDataTable $dataTable)
    {
        // return view('web.dashboard.index');
        // return $dataTable->render('web.dashboard.index');
    }
}
