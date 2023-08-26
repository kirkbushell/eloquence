<?php
namespace Eloquence\Behaviours\CountCache;

trait HasCounts
{
    public static function bootHasCounts()
    {
        static::observe(Observer::class);
    }
}
