<?php

namespace App\Models;

use App\Traits\AuthUser;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ticket extends Model
{
    use AuthUser;

    protected $fillable = ['ticket_number', 'user_id', 'status', 'draw_id'];

    protected $appends = ['full_ticket_no'];

    protected function fullTicketNo(): Attribute
    {
        return Attribute::get(
            fn () => "TN-{$this->ticket_number}"
        );
    }

    /**
     * Get the user that owns the Ticket
     */
    public function draw(): BelongsTo
    {
        return $this->belongsTo(draw::class);
    }

    public function scopeRunning(Builder $ticket): Builder
    {
        return $ticket->where('status', 'RUNNING');
    }

    public function scopeCompleted(Builder $ticket): Builder
    {
        return $ticket->where('status', 'COMPLETED');
    }
}
