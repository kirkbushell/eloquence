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

    private function __construct(private Countable $model) {}

    public static function for(Countable $model): self
    {
        return new self($model);
    }

    /**
     * Applies the provided function to the count cache setup/configuration.
     *
     * @param \Closure $function
     */
    public function apply(\Closure $function)
    {
        foreach ($this->model->countedBy() as $key => $value) {
            $function($this->config($key, $value, 'count'));
        }
    }

    /**
     * Update the count cache for all related models.
     */
    public function update()
    {
        $this->apply(function(CacheConfig $config) {
            $foreignKey = $config->foreignKeyName($this->model);

            if (!$this->model->getOriginal($foreignKey) || $this->model->$foreignKey === $this->model->getOriginal($foreignKey)) return;

            // for the minus operation, we first have to get the model that is no longer associated with this one.
            $originalRelatedModel = $config->emptyRelatedModel($this->model)->find($this->model->getOriginal($foreignKey));
            $originalRelatedModel->decrement($config->aggregateField);

            $this->increment();
        });
    }

    /**
     * Rebuild the count caches from the database
     */
    public function rebuild()
    {
        $this->apply(function($config) {
            $this->rebuildCacheRecord($config, $this->model, 'COUNT');
        });
    }

    public function increment(): void
    {
        $this->apply(fn(CacheConfig $config) => $config->relation($this->model)->increment($config->aggregateField));
    }

    public function decrement(): void
    {
        $this->apply(fn(CacheConfig $config) => $config->relation($this->model)->decrement($config->aggregateField));
    }
}
