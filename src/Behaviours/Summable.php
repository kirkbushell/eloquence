<?php
namespace Eloquence\Behaviours;

trait Summable
{
    use Cacheable;

    /**
     * Boot the trait and its event bindings when a model is created.
     */
    public static function bootSummable()
    {
        static::created(function($model) {
            $model->applyToSumCache(function($config) use ($model) {
                $amount = $model->{$config['columnToSum']};
                $model->updateCacheRecord($config, '+', $amount, $model->{$config['foreignKey']});
            });
        });

        static::updated(function($model) {
            $model->updateSumCache();
        });

        static::deleted(function($model) {
            $model->applyToSumCache(function($config) use ($model) {
                $amount = $model->{$config['columnToSum']};
                $model->updateCacheRecord($config, '-', $amount, $model->{$config['foreignKey']});
            });
        });
    }

    /**
     * Applies the provided function to the count cache setup/configuration.
     *
     * @param \Closure $function
     */
    protected function applyToSumCache(\Closure $function)
    {
        foreach ($this->sumCaches() as $key => $cache) {
            $function($this->sumCacheConfig($key, $cache));
        }
    }

    /**
     * Update the cache for all operations.
     */
    public function updateSumCache()
    {
        $this->applyToSumCache(function($config) {
            $foreignKey = $this->key($config['foreignKey']);
            $amount = $this->{$config['columnToSum']};

            if ($this->getOriginal($foreignKey) && $this->{$foreignKey} != $this->getOriginal($foreignKey)) {
                $this->updateCacheRecord($config, '-', $amount, $this->getOriginal($foreignKey));
                $this->updateCacheRecord($config, '+', $amount, $this->{$foreignKey});
            }
        });
    }

    /**
     * Takes a registered sum cache, and setups up defaults.
     *
     * @param string $cacheKey
     * @param array $cacheOptions
     * @return array
     */
    protected function sumCacheConfig($cacheKey, $cacheOptions)
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
                if (isset($cacheOptions[3])) {
                    $opts['key'] = $cacheOptions[3];
                }
                if (isset($cacheOptions[2])) {
                    $opts['foreignKey'] = $cacheOptions[2];
                }
                if (isset($cacheOptions[1])) {
                    $opts['columnToSum'] = $cacheOptions[1];
                }
                if (isset($cacheOptions[0])) {
                    $relatedModel = $cacheOptions[0];
                }
            }
        }

        return $this->sumDefaults($opts, $relatedModel);
    }

    /**
     * Returns necessary defaults, overwritten by provided options.
     *
     * @param array $options
     * @param string $relatedModel
     * @return array
     */
    protected function sumDefaults($options, $relatedModel)
    {
        $defaults = [
            'model' => $relatedModel,
            'columnToSum' => 'total',
            'field' => $this->field($this, 'total'),
            'foreignKey' => $this->field($relatedModel, 'id'),
            'key' => 'id'
        ];

        return array_merge($defaults, $options);
    }

    /**
     * Return the sum cache configuration for the model.
     *
     * @return array
     */
    public abstract function sumCaches();
}
