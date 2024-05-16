<?php

namespace Eloquence\Behaviours\CountCache;

use Eloquence\Behaviours\Cacheable;
use Eloquence\Behaviours\CacheConfig;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * The count cache does operations on the model that has just updated, works out the related models, and calls the appropriate operations on cacheable.
 */
class CountCache
{
    use Cacheable;

    private function __construct(private Model $model)
    {
    }

    private function configuration(): array
    {
        return $this->reflect(CountedBy::class, function (array $config) {
            $aggregateField = $config['attribute']->as ?? Str::lower(Str::snake(class_basename($this->model))).'_count';
            return [$config['name'] => $aggregateField];
        });
    }

    /**
     * When a model is updated, its foreign keys may have changed. In this situation, we need to update both the original
     * related model, and the new one.The original would be deducted the value, whilst the new one is increased.
     */
    public function update(): void
    {
        $this->apply(function (CacheConfig $config) {
            $foreignKey = $config->foreignKeyName($this->model);

            // We only do updates if the foreign key was actually changed
            if (!$this->model->wasChanged($foreignKey)) {
                return;
            }

            // for the minus operation, we first have to get the model that is no longer associated with this one.
            $originalRelatedModel = $config->emptyRelatedModel($this->model)->find($this->model->getOriginal($foreignKey));

            $this->updateCacheValue($originalRelatedModel, $config, -1);

            // If there is no longer a relation, nothing more to do.
            if (null === $this->model->{$foreignKey}) return;

            $this->updateCacheValue($config->relatedModel($this->model), $config, 1);
        });
    }

    /**
     * Rebuild the count caches from the database for each matching model.
     */
    public function rebuild(): void
    {
        $this->apply(function (CacheConfig $config) {
            $this->rebuildCacheRecord($config, $this->model, 'count');
        });
    }

    public function increment(): void
    {
        $this->apply(function (CacheConfig $config) {
            $this->updateCacheValue($config->relatedModel($this->model), $config, 1);
        });
    }

    public function decrement(): void
    {
        $this->apply(function (CacheConfig $config) {
            $this->updateCacheValue($config->relatedModel($this->model), $config, -1);
        });
    }
}