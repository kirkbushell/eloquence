<?php

namespace Eloquence\Behaviours;

use Closure;
use Eloquence\Behaviours\SumCache\SummedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use ReflectionClass;
use ReflectionMethod;
use Tests\Acceptance\Models\User;

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
     * Template method for setting up the CacheConfig object.
     */
    protected function config($relationName, $aggregateField): CacheConfig
    {
        return new CacheConfig($relationName, $aggregateField);
    }

    /**
     * Helper method for easier use of the implementing classes.
     */
    public static function for(Model $model): self
    {
        return new self($model);
    }

    public function reflect(string $attributeClass, \Closure $fn)
    {
        $reflect = new ReflectionClass($this->model);

        // This behemoth cycles through all valid methods, and then gets only the attributes we care about,
        // formatting it in a way that is usable by our various aggregate service classes.
        return collect($reflect->getMethods())
            ->filter(fn (ReflectionMethod $method) => count($method->getAttributes($attributeClass)) > 0)
            ->flatten()
            ->map(function (ReflectionMethod $method) use ($attributeClass) {
                return collect($method->getAttributes($attributeClass))->map(fn (\ReflectionAttribute $attribute) => [
                    'name' => $method->name,
                    'attribute' => $attribute->newInstance(),
                ])->toArray();
            })
            ->flatten(1)
            ->mapWithKeys($fn)
            ->toArray();
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
    public function rebuildCacheRecord(CacheConfig $config, Model $model, $command): void
    {
        $foreignKey = $config->foreignKeyName($model);
        $related = $config->emptyRelatedModel($model);

        $updateSql = sprintf(
            'UPDATE %s SET %s = COALESCE((SELECT %s(%s) FROM %s WHERE %s = %s.%s), 0)',
            $related->getTable(),
            $config->aggregateField,
            $command,
            $config->sourceField,
            $model->getTable(),
            $foreignKey,
            $related->getTable(),
            $related->getKeyName()
        );

        DB::update($updateSql);
    }

    /**
     * Update the cache value for the model.
     */
    protected function updateCacheValue(?Model $model, CacheConfig $config, $value): void
    {
        if(!$model){
            return;
        }

        $model->{$config->aggregateField} = $model->{$config->aggregateField} + $value;
        $model->save();
    }
}
