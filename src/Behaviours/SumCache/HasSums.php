<?php

namespace Eloquence\Behaviours\SumCache;

use Illuminate\Database\Eloquent\SoftDeletes;

trait HasSums
{
    public static function bootHasSums(): void
    {
        static::created(Observer::class.'@created');
        static::updated(Observer::class.'@updated');
        static::deleted(Observer::class.'@deleted');

        if (in_array(SoftDeletes::class, class_uses_recursive(static::class))) {
            static::restored(Observer::class.'@restored');
        }
    }

    public static function rebuildSumCache(): void
    {
        SumCache::for(new self())->rebuild();
    }
}
