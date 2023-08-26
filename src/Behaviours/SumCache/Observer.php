<?php

namespace Eloquence\Behaviours\SumCache;

class Observer
{
    public function created(Summable $model)
    {
        SumCache::for($model)->apply(function($config) use ($model, $sumCache) {
            $sumCache->updateCacheRecord($config, '+', $model->{$config['columnToSum']}, $model->{$config['foreignKey']});
        });
    }

    public function updated(Summable $model)
    {

    }

    public function deleted(Summable $model)
    {

    }

    public function restored(Summable $model)
    {

    }
}