<?php

namespace App\Models;

use App\Traits\AuthUser;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Options extends Model
{
    protected $fillable = ['user_id', 'draw_id', 'ticket_id', 'number', 'option', 'qty', 'total', 'status'];

    use AuthUser;

    /**
     * Get the user that owns the Options
     */
    public function draw(): BelongsTo
    {
        return $this->belongsTo(Draw::class);
    }

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    public function scopeForTicket(Builder $options): Builder
    {
        return $options->whereHas('ticket');
    }

    public function scopeForDraw(Builder $options): Builder
    {
        return $options->whereHas('draw');
    }

    public function scopeForRunningTicket(Builder $options): Builder
    {
        return $options->whereHas('ticket', fn ($ticket) => $ticket->running());
    }

    public function scopeForCompletedTicket(Builder $options): Builder
    {
        return $options->whereHas('ticket', fn ($ticket) => $ticket->completed());
    }
}
