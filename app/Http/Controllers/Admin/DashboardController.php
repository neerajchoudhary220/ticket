<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\DrawProfilLossDataTable;
use App\Http\Controllers\Controller;
use App\Models\TicketOption;
use App\Models\User;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index(DrawProfilLossDataTable $dataTable)
    {
        $data = [
            'total_shopkeepers' => User::count(),
            'total_draws' => TicketOption::whereDate('created_at', Carbon::today())->get()->groupBy('draw_id')->count(),
        ];

        // return view('admin.dashboard.index', compact('data'));
        return $dataTable->render('admin.dashboard.index', compact('data'));
    }
}
