<?php

namespace Eloquence\Behaviours\SumCache;

trait HasSums
{
    public static function bootHasSums(): void
    {
        static::created(Observer::class.'@created');
        static::updated(Observer::class.'@updated');
        static::deleted(Observer::class.'@deleted');
        static::restored(Observer::class.'@restored');
    }

    public static function rebuildSumCache(): void
    {
        SumCache::for(new self())->rebuild();
    }
}
