<?php

namespace Eloquence\Behaviours\ValueCache;

trait HasValues
{
    public static function bootHasValues(): void
    {
        static::observe(Observer::class);
    }

    public static function rebuildValueCache(): void
    {
        ValueCache::for(new self())->rebuild();
    }
}