<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class UserController extends Controller
{
    public function index()
    {
        return view('admin.shopkeepers.index');
    }

    public function showAddForm()
    {
        return view('admin.shopkeepers.add');
    }
}
