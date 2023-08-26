<?php
namespace Eloquence\Behaviours\SumCache;

use Eloquence\Behaviours\Cacheable;

trait HasSums
{
    use Cacheable;

    public static function bootHasSums()
    {
        static::observe(Observer::class);
    }
}
