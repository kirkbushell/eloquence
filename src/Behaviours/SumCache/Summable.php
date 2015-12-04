<?php
namespace Eloquence\Behaviours\SumCache;

use Illuminate\Support\Facades\DB;

trait Summable
{
    /**
     * Boot the trait and its event bindings when a model is created.
     */
    public static function bootSummable()
    {
        static::created(function($model) {
            $model->applyToSumCache(function($config) use ($model) {
                $amount = $model->{$config['columnToSum']};
                $model->updateSumCache($config, '+', $amount, $model->{$config['foreignKey']});
            });
        });

        static::updated(function($model) {
            $model->updateCache();
        });

        static::deleted(function($model) {
            $model->applyToSumCache(function($config) use ($model) {
                $amount = $model->{$config['columnToSum']};
                $model->updateSumCache($config, '-', $amount, $model->{$config['foreignKey']});
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
    public function updateCache()
    {
        $this->applyToSumCache(function($config) {
            $foreignKey = $this->key($config['foreignKey']);
            $amount = $this->{$config['columnToSum']};

            if ($this->getOriginal($foreignKey) && $this->{$foreignKey} != $this->getOriginal($foreignKey)) {
                $this->updateSumCache($config, '-', $amount, $this->getOriginal($foreignKey));
                $this->updateSumCache($config, '+', $amount, $this->{$foreignKey});
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
    public function updateSumCache(array $config, $operation, $amount, $foreignKey)
    {
        if (is_null($foreignKey)) {
            return;
        }

        $table = $this->getModelTable($config['model']);

        // the following is required for camel-cased models, in case users are defining their attributes as camelCase
        $field = snake_case($config['field']);
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
     * Returns the table for a given model. Model can be an Eloquent model object, or a full namespaced
     * class string.
     *
     * @param string|object $model
     * @return mixed
     */
    public function getModelTable($model)
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
            'field' => $this->field($this, 'total'),
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
     * @param string $field
     * @return mixed
     */
    protected function key($field)
    {
        if (method_exists($this, 'getTrueKey')) {
            return $this->getTrueKey($field);
        }

        return $field;
    }

    /**
     * Should return an array of the sum caches that need to be updated when this
     * model's state changes. Use the following array below as an example when an Order
     * needs to update a User's order sum cache. These represent the default values used
     * by the behaviour.
     *
     *   return [ 'order_total' => [ 'User', 'total', 'user_id', 'id' ] ];
     *
     * So, to extend, the first argument should be an index representing the sum cache
     * field on the associated model. Next is a numerical array:
     *
     * 0 = The model to be used for the update
     * 1 = The remote field that represents the value to sum *optional
     * 2 = The foreign_key for the relationship that RelatedSum will watch *optional
     * 3 = The remote field that represents the key *optional
     *
     * If the latter 2 options are not provided, or if the sum cache option is a string representing
     * the model, then RelatedSum will assume the ID fields based on conventional standards.
     *
     * Ie. another way to setup a sum cache is like below. This is an identical configuration to above.
     *
     *   return [ 'order_total' => [ 'User', 'total' ] ];
     *
     * This can be simplified even further, like this:
     *
     *   return [ 'User' ];
     *
     * @return array
     */
    public abstract function sumCaches();
}
