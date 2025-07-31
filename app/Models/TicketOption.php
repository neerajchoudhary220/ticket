<?php

namespace App\Models;

use App\Traits\AuthUser;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketOption extends Model
{
    use AuthUser;

    protected $fillable = ['user_id', 'draw_id', 'ticket_id', 'a_qty', 'b_qty', 'c_qty', 'number', 'option_id'];

    /**
     * Get the user that owns the TicketOption
     */
    public function draw(): BelongsTo
    {
        return $this->belongsTo(Draw::class);
    }

    public function scopeForTicket(Builder $TicketOption, $ticket_id): Builder
    {
        return $TicketOption->where('ticket_id', $ticket_id);
    }

    public function scopeForDraw(Builder $TicketOption, $draw_id): Builder
    {
        return $TicketOption->where('draw_id', $draw_id);
    }

    public function totalCollection($total_qty)
    {
        return $total_qty * 11;
    }

    public function totalDistributions($total_qty)
    {
        return $total_qty * 100;
    }
}
