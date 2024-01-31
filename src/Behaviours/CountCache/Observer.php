<?php

namespace Eloquence\Behaviours\CountCache;

/**
 * The Observer is used for watching for model updates and making the appropriate changes
 * as required. This includes watching for created, deleted, updated and restored events.
 */
class Observer
{
    /**
     * When the model has been created, increment the count cache by 1.
     *
     * @param $model
     */
    public function created($model): void
    {
        CountCache::for($model)->increment();
    }

    /**
     * When the model is deleted, decrement the count cache by 1.
     *
     * @param $model
     */
    public function deleted($model): void
    {
        CountCache::for($model)->decrement();
    }

    /**
     * When the model is updated, update the count cache.
     *
     * @param $model
     */
    public function updated($model): void
    {
        CountCache::for($model)->update();
    }

    /**
     * When the model is restored, again increment the count cache by 1.
     *
     * @param $model
     */
    public function restored($model): void
    {
        CountCache::for($model)->increment();
    }
}
