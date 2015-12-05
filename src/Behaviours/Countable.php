<?php
namespace Eloquence\Behaviours;

trait Countable
{
    use Cacheable;

    /**
     * Boot the countable behaviour and setup the appropriate event bindings.
     */
    public static function bootCountable()
    {
        static::created(function($model) {
            $model->applyToCountCache(function($config) use ($model) {
                $model->updateCacheRecord($config, '+', 1, $model->{$config['foreignKey']});
            });
        });

        static::updated(function($model) {
            $model->updateCountCache();
        });

        static::deleted(function($model) {
            $model->applyToCountCache(function($config) use ($model) {
                $model->updateCacheRecord($config, '-', 1, $model->{$config['foreignKey']});
            });
        });
    }

    /**
     * Applies the provided function to the count cache setup/configuration.
     *\
     * @param callable $function
     */
    protected function applyToCountCache(\Closure $function)
    {
        foreach ($this->countCaches() as $key => $cache) {
            $function($this->countCacheConfig($key, $cache));
        }
    }

    /**
     * Update the cache for all operations.
     */
    public function updateCountCache()
    {
        $this->applyToCountCache(function($config) {
            $foreignKey = $this->key($config['foreignKey']);

            if ($this->getOriginal($foreignKey) && $this->{$foreignKey} != $this->getOriginal($foreignKey)) {
                $this->updateCacheRecord($config, '-', 1, $this->getOriginal($foreignKey));
                $this->updateCacheRecord($config, '+', 1, $this->{$foreignKey});
            }
        });
    }

    /**
     * Takes a registered counter cache, and setups up defaults.
     *
     * @param string $cacheKey
     * @param array $cacheOptions
     * @return array
     */
    protected function countCacheConfig($cacheKey, $cacheOptions)
    {
        $opts = [];

        if (is_numeric($cacheKey)) {
            if (is_array($cacheOptions)) {
                // Most explicit configuration provided
                $opts = $cacheOptions;
                $relatedModel = array_get($opts, 'model');
            } else {
                // Smallest number of options provided, figure out the rest
                $relatedModel = $cacheOptions;
            }
        }
        else {
            // Semi-verbose configuration provided
            $relatedModel = $cacheOptions;
            $opts['field'] = $cacheKey;

            if (is_array($cacheOptions)) {
                if (isset($cacheOptions[2])) {
                    $opts['key'] = $cacheOptions[2];
                }
                if (isset($cacheOptions[1])) {
                    $opts['foreignKey'] = $cacheOptions[1];
                }
                if (isset($cacheOptions[0])) {
                    $relatedModel = $cacheOptions[0];
                }
            }
        }

        return $this->countDefaults($opts, $relatedModel);
    }

    /**
     * Returns necessary defaults, overwritten by provided options.
     *
     * @param array $options
     * @param string $relatedModel
     * @return array
     */
    protected function countDefaults($options, $relatedModel)
    {
        $defaults = [
            'model' => $relatedModel,
            'field' => $this->field($this, 'count'),
            'foreignKey' => $this->field($relatedModel, 'id'),
            'key' => 'id'
        ];

        return array_merge($defaults, $options);
    }

    /**
     * Return the count cache configuration for the model.
     *
     * @return array
     */
    abstract public function countCaches();
}
