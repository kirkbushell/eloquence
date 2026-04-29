<?php

namespace Eloquence\Behaviours\ValueCache;

trait HasValues
{
    public static function bootHasValues(): void
    {
        static::created(Observer::class.'@created');
        static::updated(Observer::class.'@updated');
    }

    public static function rebuildValueCache(): void
    {
        ValueCache::for(new self())->rebuild();
    }
}