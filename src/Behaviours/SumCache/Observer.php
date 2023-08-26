<?php

namespace Eloquence\Behaviours\SumCache;

class Observer
{
    public function created(Summable $model)
    {
        SumCache::for($model)->increase();
    }

    public function updated(Summable $model)
    {
        SumCache::for($model)->update();
    }

    public function deleted(Summable $model)
    {
        SumCache::for($model)->decrease();
    }

    public function restored(Summable $model)
    {

    }
}