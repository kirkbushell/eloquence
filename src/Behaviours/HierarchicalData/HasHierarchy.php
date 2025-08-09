<?php

namespace Eloquence\Behaviours\HierarchicalData;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

trait HasHierarchy
{
    public function ancestor(): BelongsTo
    {
        return $this->ancestors();
    }

    public function descendants(): BelongsToMany
    {
        $this->belongsToMany(static::class, 'ancestor_id', 'descendant_id');
    }

    public function ancestors(): BelongsToMany
    {
        return $this->belongsToMany(static::class, 'descendant_id', 'ancestor_id');
    }
}