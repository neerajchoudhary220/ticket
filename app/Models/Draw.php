<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Draw extends Model
{
    const PRICE = 11;

    protected $fillable = ['price', 'start_time', 'end_time', 'status', 'total_collection', 'total_rewards'];

    protected function price(): Attribute
    {
        return Attribute::set(
            fn () => self::PRICE
        );
    }
}
