<?php

namespace Eloquence\Behaviours;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

class CacheConfig
{
    public function __construct(readonly string $relationName, readonly string $aggregateField, readonly string $sourceField = 'id')
    {
    }

    /**
     * Returns the actual Relation object - such as BelongsTo. This method makes a call to the relationship
     * method specified on the model object, and is used to infer data about the relationship.
     */
    public function relation(Model $model): Relation
    {
        return $model->{$this->relationName}();
    }

    /**
     * Returns the current related model.
     */
    public function relatedModel(Model $model): ?Model
    {
        return $model->{$this->relationName};
    }

    /**
     * Returns -a- related model object - this object is actually empty, and is found on the query builder, used to
     * infer certain information abut the relationship that cannot be found on CacheConfig::relation.
     */
    public function emptyRelatedModel(Model $model): Model
    {
        return $this->relation($model)->getModel();
    }

    /**
     * Returns the related model class name.
     */
    public function relatedModelClass($model): string
    {
        return get_class($this->emptyRelatedModel($model));
    }

    public function foreignKeyName(Model $model): string
    {
        return $this->relation($model)->getForeignKeyName();
    }
}
