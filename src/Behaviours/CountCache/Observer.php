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
    public function created($model)
    {
        $this->update($model, '+');
    }

    /**
     * When the model is deleted, decrement the count cache by 1.
     *
     * @param $model
     */
    public function deleted($model)
    {
        $this->update($model, '-');
    }

    /**
     * When the model is updated, update the count cache.
     *
     * @param $model
     */
    public function updated($model)
    {
        (new CountCache($model))->update();
    }

    /**
     * When the model is restored, again increment the count cache by 1.
     *
     * @param $model
     */
    public function restored($model)
    {
        $this->update($model, '+');
    }

    /**
     * Handle most update operations of the count cache.
     *
     * @param $model
     * @param string $operation + or -
     */
    private function update($model, $operation)
    {
        $countCache = new CountCache($model);
        $countCache->apply(function ($config) use ($countCache, $model, $operation) {
            $countCache->updateCacheRecord($config, $operation, 1, $model->{$config['foreignKey']});
        });
    }
}
