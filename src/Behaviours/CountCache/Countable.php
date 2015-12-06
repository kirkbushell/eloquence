<?php
namespace Eloquence\Behaviours\CountCache;

trait Countable
{
    /**
     * Boot the countable behaviour and setup the appropriate event bindings.
     */
    public static function bootCountable()
    {
        static::created(function($model) {
            $countCache = new CountCache($model);
            $countCache->apply(function($config) use ($countCache, $model) {
                $countCache->updateCacheRecord($config, '+', 1, $model->{$config['foreignKey']});
            });
        });

        static::updated(function($model) {
            (new CountCache($model))->update();
        });

        static::deleted(function($model) {
            $countCache = new CountCache($model);
            $countCache->apply(function($config) use ($countCache, $model) {
                $countCache->updateCacheRecord($config, '-', 1, $model->{$config['foreignKey']});
            });
        });
    }

    /**
     * Return the count cache configuration for the model.
     *
     * @return array
     */
    abstract public function countCaches();
}
