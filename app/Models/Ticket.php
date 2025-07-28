<?php

namespace App\Models;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $fillable = ['ticket_number', 'user_id', 'status', 'draw_id'];

    public function scopeRunning(Builder $ticket): Builder
    {
        return $ticket->where('status', 'RUNNING');
    }

    public function scopeCompleted(Builder $ticket): Builder
    {
        return $ticket->where('status', 'COMPLETED');
    }
}
