<?php

namespace App\Models;

use App\Traits\AuthUser;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Options extends Model
{
    protected $fillable = ['user_id', 'draw_ids', 'ticket_id', 'number', 'option', 'qty', 'total', 'status'];

    use AuthUser;

    protected $casts = [
        'draw_ids' => 'array',
    ];

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

    public function scopeForTicket(Builder $options, $ticket_id): Builder
    {
        return $options->whereHas('ticket', function ($ticket) use ($ticket_id) {
            return $ticket->where('id', $ticket_id);
        });
    }

    public function scopeForDraw(Builder $options, $draw_id): Builder
    {
        return $options->whereHas('draw', function ($draw) use ($draw_id) {
            return $draw->where('id', $draw_id);
        });
    }

    public function scopeForRunningTicket(Builder $options): Builder
    {
        return $options->whereHas('ticket', fn ($ticket) => $ticket->running());
    }

    public function scopeForCompletedTicket(Builder $options): Builder
    {
        return $options->whereHas('ticket', fn ($ticket) => $ticket->completed());
    }

    public function scopeforCompleted(Builder $options): Builder
    {
        return $options->where('status', 'COMPLETED');
    }
}
