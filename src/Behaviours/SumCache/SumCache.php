<?php
namespace Eloquence\Behaviours\SumCache;

use Eloquence\Behaviours\Cacheable;
use Eloquence\Behaviours\CacheConfig;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class SumCache
{
    use Cacheable;

    private function __construct(private Summable $model) {}

    private function relationsMethod(): array
    {
        return $this->model->summedBy();
    }

    /**
     * Rebuild the count caches from the database
     */
    public function rebuild(): void
    {
        $this->apply(function($config) {
            $this->rebuildCacheRecord($config, $this->model, 'SUM', $config['columnToSum']);
        });
    }

    public function increase(): void
    {
        $this->apply(function(CacheConfig $config) {
            $this->updateCacheValue($config->relatedModel($this->model), $config, $this->model->{$config->sourceField});
        });
    }

    public function decrease(): void
    {
        $this->apply(function(CacheConfig $config) {
            $this->updateCacheValue($config->relatedModel($this->model), $config, -$this->model->{$config->sourceField});
        });
    }

    /**
     * Update the cache for all operations.
     */
    public function update(): void
    {
        $this->apply(function($config) {
            $foreignKey = $config->foreignKeyName($this->model);

            if (!$this->model->getOriginal($foreignKey) || $this->model->$foreignKey === $this->model->getOriginal($foreignKey)) return;

            // for the minus operation, we first have to get the model that is no longer associated with this one.
            $originalRelatedModel = $config->emptyRelatedModel($this->model)->find($this->model->getOriginal($foreignKey));

            $this->updateCacheValue($originalRelatedModel, $config, -$this->model->{$config->sourceField});
            $this->updateCacheValue($config->relatedModel($this->model), $config, $this->model->{$config->sourceField});
        });
    }

    /**
     * Takes a registered sum cache, and setups up defaults.
     */
    protected function config($relation, string|array $sourceField): CacheConfig
    {
        if (is_array($sourceField)) {
            $keys = array_keys($sourceField);
            $aggregateField = $keys[0];
            $sourceField = $sourceField[$aggregateField];
        }
        else {
            $aggregateField = 'total_'.$sourceField;
        }

        return new CacheConfig($relation, $aggregateField, $sourceField);
    }
}
