<?php
namespace Eloquence\Behaviours\SumCache;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class SumCacheManager
{
    /**
     * @var \Illuminate\Database\Eloquent\Model
     */
    private $model;

    /**
     * Increase a model's related sum cache by an amount.
     *
     * @param Model $model
     */
    public function increase(Model $model)
    {
        $this->model = $model;

        $this->applyToSumCache(function($config) {
            $amount = $this->model->{$config['columnToSum']};
            $this->update($config, '+', $amount, $this->model->{$config['foreignKey']});
        });
    }

    /**
     * Decrease a model's related sum cache by an amount.
     *
     * @param Model $model
     */
    public function decrease(Model $model)
    {
        $this->model = $model;

        $this->applyToSumCache(function($config) {
            $amount = $this->model->{$config['columnToSum']};
            $this->update($config, '-', $amount, $this->model->{$config['foreignKey']});
        });
    }
    
    /**
     * Applies the provided function to the count cache setup/configuration.
     *
     * @param \Closure $function
     */
    protected function applyToSumCache(\Closure $function)
    {
        foreach ($this->model->sumCaches() as $key => $cache) {
            $function($this->sumCacheConfig($key, $cache));
        }
    }

    /**
     * Update the cache for all operations.
     *
     * @param Model $model
     */
    public function updateCache(Model $model)
     {
         $this->model = $model;

         $this->applyToSumCache(function($config) {
             $foreignKey = $this->key($this->model, $config['foreignKey']);
             $amount = $this->model->{$config['columnToSum']};

             if ($this->model->getOriginal($foreignKey) && $this->model->{$foreignKey} != $this->model->getOriginal($foreignKey)) {
                 $this->update($config, '-', $amount, $this->model->getOriginal($foreignKey));
                 $this->update($config, '+', $amount, $this->model->{$foreignKey});
             }
         });
     }

    /**
     * Updates a table's record based on the query information provided in the $config variable.
     *
     * @param array $config
     * @param string $operation Whether to increase or decrease a value. Valid values: +/-
     * @param int|float|double $amount
     * @param string $foreignKey
     */
    protected function update(array $config, $operation, $amount, $foreignKey)
    {
        if (is_null($foreignKey)) {
            return;
        }

        $table = $this->getTable($config['model']);

        // the following is required for camel-cased models, in case users are defining their attributes as camelCase
        $field = snake_case($config['sumField']);
        $key = snake_case($config['key']);
        $foreignKey = snake_case($foreignKey);

        $sql = "UPDATE `{$table}` SET `{$field}` = `{$field}` {$operation} ({$amount}) WHERE `{$key}` = {$foreignKey}";

        return DB::update($sql);
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
            $opts['sumField'] = $cacheKey;

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
     * Returns the table for a given model. Model can be an Eloquent model object, or a full namespaced
     * class string.
     *
     * @param string|object $model
     * @return mixed
     */
    public function getTable($model)
    {
        if (! is_object($model)) {
            $model = new $model;
        }

        return $model->getTable();
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
            'sumField' => $this->field($this->model, 'total'),
            'foreignKey' => $this->field($relatedModel, 'id'),
            'key' => 'id'
        ];

        return array_merge($defaults, $options);
    }

    /**
     * Creates the key based on model properties and rules.
     *
     * @param string $model
     * @param string $field
     * @return string
     */
    protected function field($model, $field)
    {
        $class = strtolower(class_basename($model));
        $field = $class.'_'.$field;

        return $field;
    }

    /**
     * Returns the true key for a given field.
     *
     * @param object $model
     * @param string $field
     * @return mixed
     */
    protected function key($model, $field)
    {
        if (method_exists($model, 'getTrueKey')) {
            return $model->getTrueKey($field);
        }

        return $field;
    }
}
