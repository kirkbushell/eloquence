<?php
namespace Eloquence\Behaviours;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

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

        $config = $this->processConfig($config);

        $sql = DB::table($config['table'])->where($config['key'], $foreignKey);

        /*
         * Increment for + operator
         */
        if ($operation == '+') {
            return $sql->increment($config['field'], $amount);
        }

        /*
         * Decrement for - operator
         */
        return $sql->decrement($config['field'], $amount);
    }

    /**
     * Rebuilds the cache for the records in question.
     *
     * @param array $config
     * @param Model $model
     * @param $command
     * @param null $aggregateField
     * @return mixed
     */
    public function rebuildCacheRecord(array $config, Model $model, $command, $aggregateField = null)
    {
        $config = $this->processConfig($config);
        $table = $this->getModelTable($model);

        if (is_null($aggregateField)) {
            $aggregateField = $config['foreignKey'];
        } else {
            $aggregateField = Str::snake($aggregateField);
        }

        $sql = DB::table($table)->select($config['foreignKey'])->groupBy($config['foreignKey']);

        if (strtolower($command) == 'count') {
            $aggregate = $sql->count($aggregateField);
        } else if (strtolower($command) == 'sum') {
            $aggregate = $sql->sum($aggregateField);
        } else if (strtolower($command) == 'avg') {
            $aggregate = $sql->avg($aggregateField);
        } else {
            $aggregate = null;
        }

        return DB::table($config['table'])
            ->update([
                $config['field'] => $aggregate
            ]);
    }

    /**
     * Creates the key based on model properties and rules.
     *
     * @param string $model
     * @param string $field
     *
     * @return string
     */
    protected function field($model, $field)
    {
        $class = strtolower(class_basename($model));
        $field = $class . '_' . $field;

        return $field;
    }

    /**
     * Process configuration parameters to check key names, fix snake casing, etc..
     *
     * @param array $config
     * @return array
     */
    protected function processConfig(array $config)
    {
        return [
            'model'      => $config['model'],
            'table'      => $this->getModelTable($config['model']),
            'field'      => Str::snake($config['field']),
            'key'        => Str::snake($this->key($config['key'])),
            'foreignKey' => Str::snake($this->key($config['foreignKey'])),
        ];
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
    protected function getModelTable($model)
    {
        if (!is_object($model)) {
            $model = new $model;
        }

        return DB::getTablePrefix().$model->getTable();
    }

}
