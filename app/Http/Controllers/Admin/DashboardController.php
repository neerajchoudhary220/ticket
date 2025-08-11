<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\DrawProfilLossDataTable;
use App\Http\Controllers\Controller;
use App\Models\TicketOption;
use App\Models\User;

class DashboardController extends Controller
{
    public function index(DrawProfilLossDataTable $dataTable)
    {
        $data = [
            'total_shopkeepers' => User::count(),
            'total_draws' => TicketOption::get()->groupBy('draw_id')->count(),
        ];

        // return view('admin.dashboard.index', compact('data'));
        return $dataTable->render('admin.dashboard.index', compact('data'));
    }
}
