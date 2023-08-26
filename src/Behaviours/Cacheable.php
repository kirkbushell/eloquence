<?php
namespace Eloquence\Behaviours;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * The cacheable trait is concerned with the related models.
 */
trait Cacheable
{

    /**
     * Updates a table's record based on the query information provided in the $config variable.
     *
     * @param string $operation Whether to increase or decrease a value. Valid values: +/-
     */
    public function updateCacheRecord(Model $model, CacheConfig $config, string $operation): void
    {
        if ($operation == '+') {
            $this->updateCacheValue($model, $config, 1);
            return;
        }

        $this->updateCacheValue($model, $config, -1);
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

    public function updateCacheValue(Model $model, CacheConfig $config, int $amount): void
    {
        $model->{$config->countField} = $model->{$config->countField} + $amount;
        $model->save();
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
}
