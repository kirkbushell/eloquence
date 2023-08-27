<?php
namespace Eloquence\Behaviours;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * The cacheable trait is concerned with the related models.
 */
trait Cacheable
{
    /**
     * Allows cacheable to work with implementors and their unique relationship methods.
     *
     * @return array
     */
    abstract private function configuration(): array;

    /**
     * Helper method for easier use of the implementing classes.
     */
    public static function for(Model $model): self
    {
        return new self($model);
    }

    /**
     * Applies the provided function using the relevant configuration to all configured relations. Configuration
     * would be one of countedBy, summedBy, averagedBy.etc.
     */
    protected function apply(Closure $function): void
    {
        foreach ($this->configuration() as $key => $value) {
            $function($this->config($key, $value));
        }
    }

    /**
     * Updates a table's record based on the query information provided in the $config variable.
     *
     * @param string $operation Whether to increase or decrease a value. Valid values: +/-
     */
    protected function updateCacheRecord(Model $model, CacheConfig $config, string $operation, int $amount): void
    {
        $this->updateCacheValue($model, $config, $amount);
    }

    /**
     * It's a bit hard to read what's going on in this method, so let's elaborate.
     *
     * 1. Get the foreign key of the model that needs to be queried.
     * 2. Get the aggregate value for all records with that foreign key.
     * 3. Update the related model wth the relevant aggregate value.
     */
    public function rebuildCacheRecord(CacheConfig $config, Model $model, $command)
    {
        $foreignKey = $config->foreignKeyName($model);
        $value = $model->newQuery()->select($foreignKey)->groupBy($foreignKey)->$command($config->aggregateField);
        $config->relatedModel($model)->update([$config->aggregateField => $value]);
    }

    /**
     * Update the cache value for the model.
     */
    protected function updateCacheValue(Model $model, CacheConfig $config, int $amount): void
    {
        $model->{$config->aggregateField} = $model->{$config->aggregateField} + $amount;
        $model->save();
    }
}
