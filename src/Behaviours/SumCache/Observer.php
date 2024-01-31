<?php

namespace Eloquence\Behaviours\SumCache;

class Observer
{
    public function created($model)
    {
        SumCache::for($model)->increase();
    }

    public function updated($model)
    {
        SumCache::for($model)->update();
    }

    public function deleted($model)
    {
        SumCache::for($model)->decrease();
    }

    public function restored($model)
    {
        SumCache::for($model)->increase();
    }
}
