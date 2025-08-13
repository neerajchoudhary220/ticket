<?php

namespace App\Models;

use App\Traits\AuthUser;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class DrawDetail extends Model
{
    use AuthUser;

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
}
