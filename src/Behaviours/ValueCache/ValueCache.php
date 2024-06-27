<?php

namespace Eloquence\Behaviours\ValueCache;

use Eloquence\Behaviours\Cacheable;
use Eloquence\Behaviours\CacheConfig;
use Illuminate\Database\Eloquent\Model;

class ValueCache
{
    use Cacheable;

    private function __construct(private Model $model)
    {
    }

    protected function config($relationName, $sourceField): CacheConfig
    {
        $keys = array_keys($sourceField);

        $aggregateField = $keys[0];
        $sourceField = $sourceField[$aggregateField];

        return new CacheConfig($relationName, $aggregateField, $sourceField);
    }

    public function rebuild()
    {

    }

    public function updateRelated(bool $new): void
    {
        $this->apply(function(CacheConfig $config) use ($new) {
            $foreignKey = $config->foreignKeyName($this->model);

            // We only do work if the model previously existed and the source field has changed, or the model was newly created in the database.
            if (!($new || $this->model->wasChanged($config->sourceField))) {
                return;
            }

            if(!$relatedModel = $config->emptyRelatedModel($this->model)->find($this->model->$foreignKey)){
                return;
            }

            $relatedModel->{$config->aggregateField} = $this->model->{$config->sourceField};
            $relatedModel->save();
        });
    }

    private function configuration(): array
    {
        return $this->reflect(ValuedBy::class, function (array $config) {
            return [$config['name'] => [$config['attribute']->as => $config['attribute']->from]];
        });
    }
}
