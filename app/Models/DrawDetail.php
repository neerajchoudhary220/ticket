<?php

namespace App\Models;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class DrawDetail extends Model
{
    const PRICE = 11;

    protected $fillable = ['draw_id', 'start_time', 'end_time', 'claim', 'total_qty', 'date',
        'claim_a', 'claim_b', 'claim_c', 'ab', 'ac', 'bc', 'claim_ab', 'claim_ac', 'claim_bc',
        'total_cross_amt'];

    public function scopeRunningDraw(Builder $drawDetail)
    {
        $currentTime = Carbon::now()->setTimezone('Asia/Kolkata')->format('H:i');

        return $drawDetail->where('start_time', '<=', $currentTime)
            ->where('end_time', '>=', $currentTime)
            ->where('date', Carbon::today());

    }

    public function formatEndTime($format = 'h:i a')
    {
        return Carbon::createFromFormat('H:i', $this->end_time)->format($format);
    }

    public function formatStartTime($format = 'h:i a')
    {
        return Carbon::createFromFormat('H:i', $this->start_time)->format($format);
    }

    public function ticketOptions()
    {
        return $this->hasMany(TicketOption::class);
    }

    public function crossAbcDetail()
    {
        return $this->hasMany(CrossAbcDetail::class);
    }

    public function scopeForUserTicketOption(EloquentBuilder $drawDetail, $user_id): Builder
    {
        return $drawDetail->whereHas('ticketOptions', function ($ticketOption) use ($user_id) {
            return $ticketOption->where('user_id', $user_id);
        });
    }
}
