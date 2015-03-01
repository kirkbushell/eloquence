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
     * Stores the original data that was set when the model was loaded.
     *
     * @var array
     */
    private $original = [];

    /**
     * @param array $original
     */
    public function setOriginal(array $original)
    {
        $this->original = $original;
    }

    /**
     * Increase a model's related count cache by 1.
     *
     * @param Model $model
     */
    public function increment(Model $model)
    {
        $this->applyToCountCache($model, function($setup) use ($model) {
            $this->update($setup, '+', $model->{$setup['foreignKey']});
        });
    }

    /**
     * Decrease a model's related count cache by 1.
     *
     * @param Model $model
     */
    public function decrement(Model $model)
    {
        $this->applyToCountCache($model, function($setup) use ($model) {
            $this->update($setup, '-', $model->{$setup['foreignKey']});
        });
    }
    
    /**
     * Applies the provided function to the count cache setup/configuration.
     *
     * @param object $model
     * @param callable $function
     */
    protected function applyToCountCache($model, \Closure $function)
    {
        foreach ($model->countCaches() as $key => $cache) {
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
         $this->applyToCountCache($model, function($setup) use ($model) {
             if ($model->{$setup['foreignKey']} != $this->original[$setup['foreignKey']]) {
                 $this->update($setup, '-', $this->original[$setup['foreignKey']]);
                 $this->update($setup, '+', $model->{$setup['foreignKey']});
             }
         });
     }

    /**
     * Updates a table's record based on the query information provided in the $setup variable.
     *
     * @param array $setup
     * @param string $operation Whether to increment or decrement a value. Valid values: +/-
     */
    protected function update(array $setup, $operation, $value)
    {
        $params = [
            'table' => $this->getTable($setup['model']),
            'countField' => $setup['countField'],
            'operation' => $operation,
            'key' => $setup['key'],
            'value' => $value
        ];
        
        return DB::statement('UPDATE :table SET :countField = :countField :operation 1 WHERE :key = :value', $params);
    }

    /**
     * Takes a registered counter cache, and setups up defaults.
     *
     * @return array
     */
    protected function countCacheConfig($cacheKey, $cacheOptions)
    {
        $opts = [];

        // Smallest number of options provided, figure out the rest
        if (is_numeric($cacheKey)) {
            $model = $cacheOptions;
        }
        else {
            if (is_array($cacheOptions)) {
                if (isset($cacheOptions[2])) {
                    $opts['key'] = $cacheOptions[2];
                }
                if (isset($cacheOptions[1])) {
                    $opts['foreignKey'] = $cacheOptions[1];
                }
                if (isset($cacheOptions[0])) {
                    $model = $cacheOptions[0];
                }
            }

            $opts['countField'] = $cacheKey;
        }

        return $this->defaults($model, $opts);
    }

    /**
     * Returns the table for a given model. Model can be a model object, or a full namespaced
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
     * @param $model
     * @param array $options
     * @return array
     */
    protected function defaults($model, $options)
    {
        $class = strtolower(class_basename($this->model));

        $defaults = [
            'model' => $model,
            'countField' => $class.'_count',
            'foreignKey' => strtolower($model.'_id'),
            'key' => 'id'
        ];

        return array_merge($defaults, $options);
    }
}
