<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class UserDraw extends Pivot
{
    protected $table = 'user_draws';

    protected $fillable = ['user_id', 'draw_id', 'total_draws'];
}
