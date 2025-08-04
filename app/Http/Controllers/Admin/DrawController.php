<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\Admin\DrawDataTable;
use App\DataTables\Admin\DrawDetailsDataTable;
use App\DataTables\Admin\NumberTicketListDataTable;
use App\DataTables\Admin\TicketDetailsDataTable;
use App\Http\Controllers\Controller;
use App\Models\Draw;
use App\Models\Ticket;
use Illuminate\Http\Request;

class DrawController extends Controller
{
    public function index(DrawDataTable $dataTable)
    {
        return $dataTable->render('admin.draw.index');
        // return view('admin.draw.index');
    }

    public function drawDetails(DrawDetailsDataTable $dataTable, Request $request)
    {
        $draw = $this->findDraw($request->draw_id);

        return $dataTable->render('admin.draw.draw-details-table', compact('draw'));
        // return view('admin.draw.draw-details-table');
    }

    public function numberList(NumberTicketListDataTable $dataTable, Request $request)
    {
        $draw = $this->findDraw($request->draw_id);
        $number = $request->number;

        return $dataTable->render('admin.draw.number-list', compact('draw', 'number'));

        // return view('admin.draw.number-list', compact('draw', 'number'));

    }

    public function ticketDetailsList(TicketDetailsDataTable $dataTable, Request $request)
    {

        $ticket = Ticket::findOrFail($request->ticket_id);
        $draw = $this->findDraw($request->draw_id);
        $number = $request->number;

        return $dataTable->render('admin.draw.ticket-details-list', compact('draw', 'ticket', 'number'));

        // return view('admin.draw.ticket-details-list');

    }

    private function findDraw($draw_id)
    {
        return Draw::findOrFail($draw_id);
    }

    public function addDraw()
    {
        return view('admin.draw.add-draw');
    }
}
