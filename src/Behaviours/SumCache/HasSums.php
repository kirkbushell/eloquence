<?php
namespace Eloquence\Behaviours\SumCache;

trait HasSums
{
    public static function bootHasSums()
    {
        static::observe(Observer::class);
    }

    public static function rebuildSumCache(): void
    {
        SumCache::for(new self)->rebuild();
    }
}
