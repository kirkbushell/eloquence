<?php
namespace Eloquence\Behaviours\SumCache;

use Eloquence\Behaviours\Cacheable;

trait HasSums
{
    use Cacheable;

    public static function bootHasCounts()
    {
        static::observe(Observer::class);
    }

    /**
     * Boot the trait and its event bindings when a model is created.
     */
    public static function bootHasSums()
    {
        static::created(function ($model) {
            $sumCache = SumCache::for($model);
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
}
