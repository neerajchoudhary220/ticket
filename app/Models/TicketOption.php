<?php

namespace App\Models;

use App\Traits\AuthUser;
use Illuminate\Database\Eloquent\Model;

class TicketOption extends Model
{
    use AuthUser;

    protected $fillable = ['user_id', 'draw_id', 'ticket_id', 'a_qty', 'b_qty', 'c_qty', 'number', 'option_id'];
}
