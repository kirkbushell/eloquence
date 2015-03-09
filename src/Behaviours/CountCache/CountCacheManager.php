<?php
namespace Eloquence\Behaviours\CountCache;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CountCacheManager
{
    /**
     * @var \Illuminate\Database\Eloquent\Model
     */
    private $model;

    /**
     * Increase a model's related count cache by 1.
     *
     * @param Model $model
     */
    public function increment(Model $model)
    {
        $this->model = $model;

        $this->applyToCountCache(function($config) {
            $this->update($config, '+', $this->model->{$config['foreignKey']});
        });
    }

    /**
     * Decrease a model's related count cache by 1.
     *
     * @param Model $model
     */
    public function decrement(Model $model)
    {
        $this->model = $model;

        $this->applyToCountCache(function($config) {
            $this->update($config, '-', $this->model->{$config['foreignKey']});
        });
    }
    
    /**
     * Applies the provided function to the count cache setup/configuration.
     *\
     * @param callable $function
     */
    protected function applyToCountCache(\Closure $function)
    {
        foreach ($this->model->countCaches() as $key => $cache) {
            $function($this->countCacheConfig($key, $cache));
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

         $this->applyToCountCache(function($config) {
             $foreignKey = $this->key($this->model, $config['foreignKey']);

             if ($this->model->getOriginal($foreignKey) && $this->model->{$foreignKey} != $this->model->getOriginal($foreignKey)) {
                 $this->update($config, '-', $this->model->getOriginal($foreignKey));
                 $this->update($config, '+', $this->model->{$foreignKey});
             }
         });
     }

    /**
     * Updates a table's record based on the query information provided in the $config variable.
     *
     * @param array $config
     * @param string $operation Whether to increment or decrement a value. Valid values: +/-
     * @param string $foreignKey
     * @return
     */
    protected function update(array $config, $operation, $foreignKey)
    {
        $table = $this->getTable($config['model']);

        // the following is required for camel-cased models, in case users are defining their attributes as camelCase
        $field = snake_case($config['countField']);
        $key = snake_case($config['key']);
        $foreignKey = snake_case($foreignKey);

        $sql = "UPDATE `{$table}` SET `{$field}` = `{$field}` {$operation} 1 WHERE `{$key}` = {$foreignKey}";

        return DB::update($sql);
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

        // Smallest number of options provided, figure out the rest
        if (is_numeric($cacheKey)) {
            $relatedModel = $cacheOptions;
        }
        else {
            $relatedModel = $cacheOptions;
            $opts['countField'] = $cacheKey;

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
            'countField' => $this->field($this->model, 'count'),
            'foreignKey' => $this->field($relatedModel, 'id'),
            'key' => 'id'
        ];

        return array_merge($defaults, $options);
    }

    /**
     * Creates the key based on model properties and rules.
     *
     * @param string $class
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
