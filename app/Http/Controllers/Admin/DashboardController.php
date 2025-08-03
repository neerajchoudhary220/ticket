<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Draw;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $data = [
            'total_shopkeepers' => User::count(),
            'total_draws' => Draw::count(),
        ];

        return view('admin.dashboard.index', compact('data'));
    }
}
