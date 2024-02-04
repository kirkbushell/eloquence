<?php

namespace Eloquence\Behaviours\CountCache;

trait HasCounts
{
    public static function bootHasCounts(): void
    {
        static::observe(Observer::class);
    }

    public static function rebuildCountCache(): void
    {
        CountCache::for(new self())->rebuild();
    }
}
