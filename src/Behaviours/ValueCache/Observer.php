<?php

namespace Eloquence\Behaviours\ValueCache;

class Observer
{
    public function created($model): void
    {
        ValueCache::for($model)->updateRelated(true);
    }

    public function updated($model): void
    {
        ValueCache::for($model)->updateRelated(false);
    }
}