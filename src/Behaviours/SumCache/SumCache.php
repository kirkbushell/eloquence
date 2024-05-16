<?php

namespace Eloquence\Behaviours\SumCache;

use Eloquence\Behaviours\Cacheable;
use Eloquence\Behaviours\CacheConfig;
use Illuminate\Database\Eloquent\Model;

class SumCache
{
    use Cacheable;

    private function __construct(private Model $model)
    {
    }

    private function configuration(): array
    {
        return $this->reflect(SummedBy::class, function (array $config) {
            return [$config['name'] => [$config['attribute']->as => $config['attribute']->from]];
        });
    }

    /**
     * Rebuild the count caches from the database
     */
    public function rebuild(): void
    {
        $this->apply(function ($config) {
            $this->rebuildCacheRecord($config, $this->model, 'sum');
        });
    }

    public function increase(): void
    {
        $this->apply(function (CacheConfig $config) {
            $this->updateCacheValue($config->relatedModel($this->model), $config, (int) $this->model->{$config->sourceField});
        });
    }

    public function decrease(): void
    {
        $this->apply(function (CacheConfig $config) {
            $this->updateCacheValue($config->relatedModel($this->model), $config, -(int) $this->model->{$config->sourceField});
        });
    }

    /**
     * Update the cache for all operations.
     */
    public function update(): void
    {
        $this->apply(function (CacheConfig $config) {
            $foreignKey = $config->foreignKeyName($this->model);

            if ($this->model->wasChanged($foreignKey)) {
                // for the minus operation, we first have to get the model that is no longer associated with this one.
                $originalRelatedModel = $config->emptyRelatedModel($this->model)->find($this->model->getOriginal($foreignKey));
                $this->updateCacheValue($originalRelatedModel, $config, -$this->model->getOriginal($config->sourceField));

                if (null === $this->model->{$foreignKey}) return;

                $this->updateCacheValue($config->relatedModel($this->model), $config, $this->model->{$config->sourceField});
            } else {
                $difference = $this->model->{$config->sourceField} - $this->model->getOriginal($config->sourceField);
                $this->updateCacheValue($config->relatedModel($this->model), $config, $difference);
            }
        });
    }

    /**
     * Takes a registered sum cache, and setups up defaults.
     */
    protected function config($relationName, $sourceField): CacheConfig
    {
        $keys = array_keys($sourceField);

        $aggregateField = $keys[0];
        $sourceField = $sourceField[$aggregateField];

        return new CacheConfig($relationName, $aggregateField, $sourceField);
    }
}