<?php
namespace Eloquence\Behaviours\SumCache;

use Eloquence\Behaviours\Cacheable;

trait Summable
{
    use Cacheable;

    /**
     * Boot the trait and its event bindings when a model is created.
     */
    public static function bootSummable()
    {
        static::created(function ($model) {
            $sumCache = new SumCache($model);
            $sumCache->apply(function ($config) use ($model, $sumCache) {
                $sumCache->updateCacheRecord($config, '+', $model->{$config['columnToSum']}, $model->{$config['foreignKey']});
            });
        });

        static::updated(function ($model) {
            (new SumCache($model))->update();
        });

        static::deleted(function ($model) {
            $sumCache = new SumCache($model);
            $sumCache->apply(function ($config) use ($model, $sumCache) {
                $sumCache->updateCacheRecord($config, '-', $model->{$config['columnToSum']}, $model->{$config['foreignKey']});
            });
        });
    }

    /**
     * Return the sum cache configuration for the model.
     *
     * @return array
     */
    abstract public function sumCaches();
}
