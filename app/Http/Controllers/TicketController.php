<?php

namespace App\Http\Controllers;

class TicketController extends Controller
{
    public function index()
    {
        return view('web.ticket.ticket-list');
    }

    public function addTicket()
    {
        return view('web.ticket.add-ticket');
    }
}
