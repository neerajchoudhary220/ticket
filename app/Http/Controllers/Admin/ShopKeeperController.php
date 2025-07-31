<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\Admin\ShopkeepersDataTable;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class ShopKeeperController extends Controller
{
    public function index(ShopkeepersDataTable $dataTable)
    {
        return $dataTable->render('admin.shopkeepers.index');
    }

    public function addEditShopKeeper(Request $request)
    {
        $user = null;
        if ($request->user_id) {
            $user = User::findOrfail($request->user_id);
        }

        return view('admin.shopkeepers.add', compact('user'));
    }
}
