<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\DrawProfilLossDataTable;
use App\Http\Controllers\Controller;
use App\Models\DrawDetail;
use App\Models\User;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index(DrawProfilLossDataTable $dataTable)
    {
        $data = [
            'total_shopkeepers' => User::count(),
            'total_draws' => DrawDetail::where('date', Carbon::today())->count(),
            'claimed' => DrawDetail::where('date', Carbon::today())
                ->where('claim', '!=', 0)
                ->count(),

        ];

        // return view('admin.dashboard.index', compact('data'));
        return $dataTable->render('admin.dashboard.index', compact('data'));
    }
}
