<?php
namespace Eloquence\Behaviours\CountCache;

trait HasCounts
{
    /**
     * Boot the countable behaviour and setup the appropriate event bindings.
     */
    public static function bootHasCounts()
    {
        static::observe(Observer::class);
    }
}
