<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait AuthUser
{
    /**
     * Scope a query to only include records for the given user ID.
     */
    public function scopeForUser(Builder $query, ?int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }
}
