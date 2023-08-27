<?php
namespace Eloquence\Behaviours\CountCache;

use Eloquence\Behaviours\Cacheable;
use Eloquence\Behaviours\CacheConfig;
use Illuminate\Support\Str;

/**
 * The count cache does operations on the model that has just updated, works out the related models, and calls the appropriate operations on cacheable.
 */
class CountCache
{
    use Cacheable;

    private function __construct(private Countable $model) {}

    private function configuration(): array
    {
        return $this->model->countedBy();
    }

    /**
     * When a model is updated, its foreign keys may have changed. In this situation, we need to update both the original
     * related model, and the new one.The original would be deducted the value, whilst the new one is increased.
     */
    public function update(): void
    {
        $this->apply(function(CacheConfig $config) {
            $foreignKey = $config->foreignKeyName($this->model);

            if (!$this->model->getOriginal($foreignKey) || $this->model->$foreignKey === $this->model->getOriginal($foreignKey)) return;

            // for the minus operation, we first have to get the model that is no longer associated with this one.
            $originalRelatedModel = $config->emptyRelatedModel($this->model)->find($this->model->getOriginal($foreignKey));

            $this->updateCacheValue($originalRelatedModel, $config, -1);
            $this->updateCacheValue($config->relatedModel($this->model), $config, 1);
        });
    }

    /**
     * Rebuild the count caches from the database
     */
    public function rebuild(): void
    {
        $this->apply(function($config) {
            $this->rebuildCacheRecord($config, $this->model, 'count');
        });
    }

    public function increment(): void
    {
        $this->apply(function(CacheConfig $config) {
            $this->updateCacheValue($config->relatedModel($this->model), $config, 1);
        });
    }

    public function decrement(): void
    {
        $this->apply(function(CacheConfig $config) {
            $this->updateCacheValue($config->relatedModel($this->model), $config, -1);
        });
    }

    /**
     * Takes a registered counter cache, and setups up defaults.
     */
    protected function config($key, string $value): CacheConfig
    {
        // If the key is numeric, it means only the relationship method has been referenced.
        if (is_numeric($key)) {
            $key = $value;
            $value = Str::lower(Str::snake(class_basename($this->model))).'_count';
        }

        return new CacheConfig($key, $value);
    }
}
