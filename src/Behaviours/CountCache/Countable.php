<?php
namespace Eloquence\Behaviours\CountCache;

trait Countable
{
    /**
     * Boot the countable behaviour and setup the appropriate event bindings.
     */
    public static function bootCountable()
    {
        static::observe(Observer::class);
    }
}
