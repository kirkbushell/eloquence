<?php

namespace Eloquence\Behaviours\CountCache;

trait HasCounts
{
    public static function bootHasCounts(): void
    {
        static::created(Observer::class.'@created');
        static::updated(Observer::class.'@updated');
        static::deleted(Observer::class.'@deleted');
        static::restored(Observer::class.'@restored');
    }

    public static function rebuildCountCache(): void
    {
        CountCache::for(new self())->rebuild();
    }
}
