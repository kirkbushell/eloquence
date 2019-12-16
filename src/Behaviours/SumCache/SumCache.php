<?php
namespace Eloquence\Behaviours\SumCache;

use Eloquence\Behaviours\Cacheable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class SumCache
{
    use Cacheable;

    /**
     * @var Model
     */
    private $model;

    /**
     * @param Model $model
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * Applies the provided function to the count cache setup/configuration.
     *
     * @param \Closure $function
     */
    public function apply(\Closure $function)
    {
        foreach ($this->model->sumCaches() as $key => $cache) {
            $function($this->config($key, $cache));
        }
    }

    /**
     * Rebuild the count caches from the database
     */
    public function rebuild()
    {
        $this->apply(function($config) {
            $this->rebuildCacheRecord($config, $this->model, 'SUM', $config['columnToSum']);
        });
    }

    /**
     * Update the cache for all operations.
     */
    public function update()
    {
        $this->apply(function ($config) {
            $foreignKey = Str::snake($this->key($config['foreignKey']));
            $amount = $this->model->{$config['columnToSum']};

            if ($this->model->getOriginal($foreignKey) && $this->model->{$foreignKey} != $this->model->getOriginal($foreignKey)) {
                $this->updateCacheRecord($config, '-', $amount, $this->model->getOriginal($foreignKey));
                $this->updateCacheRecord($config, '+', $amount, $this->model->{$foreignKey});
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
    protected function config($cacheKey, $cacheOptions)
    {
        $opts = [];

        if (is_numeric($cacheKey)) {
            if (is_array($cacheOptions)) {
                // Most explicit configuration provided
                $opts = $cacheOptions;
                $relatedModel = Arr::get($opts, 'model');
            } else {
                // Smallest number of options provided, figure out the rest
                $relatedModel = $cacheOptions;
            }
        } else {
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

        return $this->defaults($opts, $relatedModel);
    }

    /**
     * Returns necessary defaults, overwritten by provided options.
     *
     * @param array $options
     * @param string $relatedModel
     * @return array
     */
    protected function defaults($options, $relatedModel)
    {
        $defaults = [
            'model' => $relatedModel,
            'columnToSum' => 'total',
            'field' => $this->field($this->model, 'total'),
            'foreignKey' => $this->field($relatedModel, 'id'),
            'key' => 'id'
        ];

        return array_merge($defaults, $options);
    }
}
