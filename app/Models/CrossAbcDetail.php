<?php

namespace App\Models;

use App\Traits\AuthUser;
use Illuminate\Database\Eloquent\Model;

class CrossAbcDetail extends Model
{
    use AuthUser;

    protected $fillable = ['user_id', 'ticket_id', 'ab_number', 'ab_amt', 'ac_number',
        'ac_amt', 'bc_number', 'bc_amt'];
}
