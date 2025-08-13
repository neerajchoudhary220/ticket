<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\Admin\DrawDataTable;
use App\DataTables\Admin\DrawDetailsDataTable;
use App\DataTables\Admin\NumberTicketListDataTable;
use App\DataTables\Admin\ShopKeeperDrawDetailsDataTable;
use App\DataTables\Admin\TicketDetailsDataTable;
use App\Http\Controllers\Controller;
use App\Models\Draw;
use App\Models\DrawDetail;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Request;

class DrawController extends Controller
{
    public function index(DrawDataTable $dataTable)
    {
        return $dataTable->render('admin.draw.index');
        // return view('admin.draw.index');
    }

    public function drawDetails(DrawDetailsDataTable $dataTable, DrawDetail $drawDetail)
    {

        return $dataTable->render('admin.draw.draw-details-table', compact('drawDetail'));
        // return view('admin.draw.draw-details-table');
    }

    public function shopKeeperDrawDetails(ShopKeeperDrawDetailsDataTable $dataTable, DrawDetail $drawDetail, User $user)
    {

        return $dataTable->render('admin.draw.shopkeeper-draw-details', compact('drawDetail', 'user'));
        // return view('admin.draw.shopkeeper-draw-details');
    }

    public function numberList(NumberTicketListDataTable $dataTable, Request $request)
    {
        $draw = $this->findDraw($request->draw_id);
        $number = $request->number;

        return $dataTable->render('admin.draw.number-list', compact('draw', 'number'));

        // return view('admin.draw.number-list', compact('draw', 'number'));

    }

    public function ticketDetailsList(TicketDetailsDataTable $dataTable, DrawDetail $drawDetail, Ticket $ticket, User $user)
    {

        return $dataTable->render('admin.draw.ticket-details-list', compact('drawDetail', 'ticket', 'user'));

        // return view('admin.draw.ticket-details-list');

    }

    private function findDraw($draw_id)
    {
        return Draw::findOrFail($draw_id);
    }

    public function addDraw(Request $request)
    {
        $draw = null;
        if ($request->draw_id) {
            $draw = $this->findDraw($request->draw_id);
        }

        return view('admin.draw.add-draw', compact('draw'));
    }
}
