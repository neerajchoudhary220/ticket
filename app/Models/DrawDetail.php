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
        'claim_a', 'claim_b', 'claim_c'];

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

    public function scopeForUserTicketOption(EloquentBuilder $drawDetail, $user_id, $start_date = null, $end_date = null, $day = null): Builder
    {
        return $drawDetail->whereHas('ticketOptions', function ($ticketOption) use ($user_id, $start_date, $end_date, $day) {
            return $ticketOption->where('user_id', $user_id)
                ->when($start_date, function ($ticketOption) use ($start_date) {
                    return $ticketOption->whereDate('created_at', $start_date);
                })
                ->when($end_date, function ($ticketOption) use ($end_date) {
                    return $ticketOption->whereDate('created_at', $end_date);
                })
                ->when($start_date && $end_date, function ($ticketOption) use ($start_date, $end_date) {
                    return $ticketOption->whereBetween('created_at', [$start_date, $end_date]);
                })
                ->when($day, function ($query) use ($day) {

                    if ($day === 'Today') {
                        $query->whereDate('date', now());
                    } elseif ($day === 'Yesterday') {
                        $query->whereDate('created_at', now()->subDay());
                    } elseif ($day === 'Last 7 Days') {
                        $query->whereBetween('created_at', [now()->subDays(6), now()]);
                    } elseif ($day === 'Last 30 Days') {
                        $query->whereBetween('created_at', [now()->subDays(29), now()]);
                    } elseif ($day === 'This Month') {
                        $query->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()]);
                    } elseif ($day === 'Last Month') {
                        $query->whereBetween('created_at', [now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth()]);
                    }
                });
        });
    }
}
