<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Draw extends Model
{
    const PRICE = 11;

    protected $fillable = ['price', 'start_time', 'end_time', 'status', 'total_collection', 'total_rewards'];

    protected $appends = ['draw_number'];

    protected function price(): Attribute
    {
        return Attribute::set(
            fn () => self::PRICE
        );
    }

    protected function drawNumber(): Attribute
    {
        return Attribute::get(
            fn () => 'DN - '.$this->id
        );
    }

    public function scopeRunningDraw(Builder $draw)
    {
        $currentTime = Carbon::now()->setTimezone('Asia/Kolkata')->format('H:i');

        return $draw->where('start_time', '<=', $currentTime)
            ->where('end_time', '>=', $currentTime);
    }

    public function formatEndTime($format = 'h:i a')
    {
        return Carbon::createFromFormat('H:i', $this->end_time)->format($format);
    }

    public function formatStartTime($format = 'h:i a')
    {
        return Carbon::createFromFormat('H:i', $this->start_time)->format($format);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_draws');
    }

    public function scopeForUser(Builder $draw, $auth_id): Builder
    {
        return $draw->whereHas('users', function ($q) use ($auth_id) {
            return $q->where('user_id', $auth_id);
        });
    }
}
