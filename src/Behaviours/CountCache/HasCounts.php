<?php

namespace Eloquence\Behaviours\CountCache;

use Illuminate\Database\Eloquent\SoftDeletes;

trait HasCounts
{
    public static function bootHasCounts(): void
    {
        static::created(Observer::class.'@created');
        static::updated(Observer::class.'@updated');
        static::deleted(Observer::class.'@deleted');

        if (in_array(SoftDeletes::class, class_uses_recursive(static::class))) {
            static::restored(Observer::class.'@restored');
        }
    }

    public static function rebuildCountCache(): void
    {
        CountCache::for(new self())->rebuild();
    }
}
