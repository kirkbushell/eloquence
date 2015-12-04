<?php
namespace Eloquence\Behaviours\CountCache;

use Illuminate\Support\Facades\DB;

trait Countable
{
    /**
     * Boot the countable behaviour and setup the appropriate event bindings.
     */
    public static function bootCountable()
    {
        static::created(function($model) {
            $model->applyToCountCache(function($config) use ($model) {
                $model->updateCountCache($config, '+', $model->{$config['foreignKey']});
            });
        });

        static::updated(function($model) {
            $model->updateCache();
        });

        static::deleted(function($model) {
            $model->applyToCountCache(function($config) use ($model) {
                $model->updateCountCache($config, '-', $model->{$config['foreignKey']});
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
    public function updateCache()
    {
        $this->applyToCountCache(function($config) {
            $foreignKey = $this->key($config['foreignKey']);

            if ($this->getOriginal($foreignKey) && $this->{$foreignKey} != $this->getOriginal($foreignKey)) {
                $this->updateCountCache($config, '-', $this->getOriginal($foreignKey));
                $this->updateCountCache($config, '+', $this->{$foreignKey});
            }
        });
    }

    /**
     * Updates a table's record based on the query information provided in the $config variable.
     *
     * @param array $config
     * @param string $operation Whether to increment or decrement a value. Valid values: +/-
     * @param string $foreignKey
     */
    protected function updateCountCache(array $config, $operation, $foreignKey)
    {
        if (is_null($foreignKey)) {
            return;
        }

        $table = $this->getModelTable($config['model']);

        // the following is required for camel-cased models, in case users are defining their attributes as camelCase
        $field = snake_case($config['countField']);
        $key = snake_case($config['key']);
        $foreignKey = snake_case($foreignKey);

        // Execute as a single query as this is more performant and works atomically across both MyISAM and
        // transactional database engines, resulting in a consistent result.
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
            'countField' => $this->field($this, 'count'),
            'foreignKey' => $this->field($relatedModel, 'id'),
            'key' => 'id'
        ];

        return array_merge($defaults, $options);
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
     * Creates the key based on model properties and rules.
     *
     * @param $model
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
     * Should return an array of the count caches that need to be updated when this
     * model's state changes. Use the following array below as an example when a User
     * needs to update a Role's user count cache. These represent the default values used
     * by the behaviour.
     *
     *   return [ 'user_count' => [ 'Role', 'role_id', 'id' ] ];
     *
     * So, to extend, the first argument should be an index representing the counter cache
     * field on the associated model. Next is a numerical array:
     *
     * 0 = The model to be used for the update
     * 1 = The foreign_key for the relationship that RelatedCount will watch *optional
     * 2 = The remote field that represents the key *optional
     *
     * If the latter 2 options are not provided, or if the counter cache option is a string representing
     * the model, then RelatedCount will assume the ID fields based on conventional standards.
     *
     * Ie. another way to setup a counter cache is like below. This is an identical configuration to above.
     *
     *   return [ 'user_count' => 'Role' ];
     *
     * This can be simplified even further, like this:
     *
     *   return [ 'Role' ];
     *
     * @return array
     */
    abstract public function countCaches();
}
