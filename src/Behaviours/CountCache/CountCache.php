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
            $function($this->config($key, $value));
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

//            dd($this->model);
            // for the minus operation, we first have to get the model that is no longer associated with this one.
            $originalRelatedModel = $config->emptyRelatedModel($this->model)->find($this->model->getOriginal($foreignKey));

            $originalRelatedModel->decrement($config->countField);

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
        $this->apply(fn(CacheConfig $config) => $config->relation($this->model)->increment($config->countField));
    }

    public function decrement(): void
    {
        $this->apply(fn(CacheConfig $config) => $config->relation($this->model)->decrement($config->countField));
    }

    /**
     * Takes a registered counter cache, and setups up defaults.
     */
    protected function config($key, string $value): CacheConfig
    {
        // If the key is numeric, it means only the relationship method has been referenced.
        if (is_numeric($key)) {
            $key = $value;
            $value = $this->defaultCountField();
        }

        return new CacheConfig($key, $value);
    }

    private function defaultCountField(): string
    {
        return Str::lower(Str::snake(class_basename($this->model))).'_count';
    }
}
