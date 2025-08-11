<?php

namespace App\Http\Controllers;

use App\DataTables\DrawProfilLossDataTable;
use App\DataTables\NumberListDataTable;
use App\DataTables\TicketDetailsDataTable;
use App\DataTables\UserDrawDetailsDataTable;
use App\Models\Draw;
use App\Models\Ticket;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(DrawProfilLossDataTable $dataTable)
    {
        // return view('web.dashboard.index');

        return $dataTable->render('web.dashboard.index');

    }

    public function addTicket(Request $request)
    {
        $ticket = null;
        $number = $request->number ?: null;

        if ($request->ticket_id) {
            $ticket = Ticket::where('id', $request->ticket_id)
                ->where('user_id', $request->user()->id)
                ->firstOrFail();
        }

        return view('web.ticket.add-ticket', compact('ticket', 'number'));
    }

    public function optionList(TicketDetailsDataTable $dataTable, Request $request)
    {
        $draw = Draw::findOrFail($request->draw_id);

        return $dataTable->render('web.dashboard.option-list', compact('draw'));
    }

    public function drawDetailsList(UserDrawDetailsDataTable $dataTable, Request $request)
    {
        // return view('web.dashboard.draw-details-datatable');
        $draw = Draw::findOrFail($request->draw_id);

        return $dataTable->render('web.dashboard.draw-details-datatable', compact('draw'));

    }

    public function numberDetailsList(NumberListDataTable $dataTable, Request $request)
    {
        $draw = $this->findDraw($request->draw_id);
        $number = $request->number;

        // return view('web.dashboard.ticket-number-details-list');
        return $dataTable->render('web.dashboard.ticket-number-details-list', compact('draw', 'number'));

    }

    private function findDraw($draw_id)
    {
        return Draw::findOrFail($draw_id);
    }
}
