<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\Admin\ShopkeepersDataTable;
use App\Http\Controllers\Controller;

class ShopKeeperController extends Controller
{
    public function index(ShopkeepersDataTable $dataTable)
    {
        // return view('admin.shopkeepers.index');
        return $dataTable->render('admin.shopkeepers.index');
    }

    public function showAddForm()
    {
        return view('admin.shopkeepers.add');
    }
}
