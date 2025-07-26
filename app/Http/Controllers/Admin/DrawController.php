<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\Admin\DrawDataTable;
use App\Http\Controllers\Controller;

class DrawController extends Controller
{
    public function index(DrawDataTable $dataTable)
    {
        return $dataTable->render('admin.draw.index');
        // return view('admin.draw.index');
    }

    public function addDraw()
    {
        return view('admin.draw.add-draw');
    }
}
