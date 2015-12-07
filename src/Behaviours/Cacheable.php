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

        $config = $this->processConfig($config);

        $sql = "UPDATE `{$config['table']}` SET `{$config['field']}` = `{$config['field']}` {$operation} {$amount} WHERE `{$config['key']}` = {$foreignKey}";

        return DB::update($sql);
    }

    public function rebuildCacheRecord(array $config, Model $model, $command, $aggregateField = null)
    {
        $config = $this->processConfig($config);

        $modelTable = $this->getModelTable($model);

        if (is_null($aggregateField)) {
            $aggregateField = $config['foreignKey'];
        } else {
            $aggregateField = snake_case($aggregateField);
        }

        $sql = "UPDATE `{$config['table']}` INNER JOIN (" .
            "SELECT `{$config['foreignKey']}`, {$command}(`{$aggregateField}`) AS aggregate FROM `{$modelTable}` GROUP BY `{$config['foreignKey']}`) a " .
            "ON (a.`{$config['foreignKey']}` = `{$config['table']}`.`{$config['key']}`" .
            ") SET `{$config['field']}` = aggregate";

        return DB::update($sql);
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
     *
     * @return array
     */
    protected function processConfig(array $config)
    {
        return [
            'model'      => $config['model'],
            'table'      => $this->getModelTable($config['model']),
            'field'      => snake_case($config['field']),
            'key'        => snake_case($this->key($config['key'])),
            'foreignKey' => snake_case($this->key($config['foreignKey'])),
        ];
    }

    /**
     * Returns the true key for a given field.
     *
     * @param string $field
     *
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
     *
     * @return mixed
     */
    protected function getModelTable($model)
    {
        if (!is_object($model)) {
            $model = new $model;
        }

        return $model->getTable();
    }

}
