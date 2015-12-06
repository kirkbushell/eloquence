<?php
namespace Eloquence\Behaviours;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

trait Cacheable
{
    /**
     * Updates a table's record based on the query information provided in the $config variable.
     *
     * @param array $config
     * @param string $operation Whether to increase or decrease a value. Valid values: +/-
     * @param int|float|double $amount
     * @param string $foreignKey
     */
    public function updateCacheRecord(array $config, $operation, $amount, $foreignKey)
    {
        if (is_null($foreignKey)) {
            return;
        }

        $table = $this->getModelTable($config['model']);

        // the following is required for camel-cased models, in case users are defining their attributes as camelCase
        $field = snake_case($config['field']);
        $key = snake_case($config['key']);
        $foreignKey = snake_case($foreignKey);

        $sql = "UPDATE `{$table}` SET `{$field}` = `{$field}` {$operation} {$amount} WHERE `{$key}` = {$foreignKey}";

        return DB::update($sql);
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
        if (method_exists($this->model, 'getTrueKey')) {
            return $this->model->getTrueKey($field);
        }

        return $field;
    }

    /**
     * Returns the table for a given model. Model can be an Eloquent model object, or a full namespaced
     * class string.
     *
     * @param string|Model $model
     * @return mixed
     */
    public function getModelTable($model)
    {
        if (!is_object($model)) {
            $model = new $model;
        }

        return $model->getTable();
    }
}