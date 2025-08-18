<?php

namespace App\Models;

use App\Traits\AuthUser;
use App\Traits\DrawDetailsTrait;
use Illuminate\Database\Eloquent\Model;

class CrossAbcDetail extends Model
{
    use AuthUser,DrawDetailsTrait;

    protected $fillable = ['user_id', 'ticket_id', 'draw_detail_id', 'option', 'number', 'type', 'combination', 'amount'];
}
