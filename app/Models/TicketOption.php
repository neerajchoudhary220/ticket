<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketOption extends Model
{
    protected $fillable = ['user_id', 'draw_id', 'ticket_number', 'a_qty', 'b_qty', 'c_qty', 'number'];
}
