<?php

namespace Eloquence\Behaviours\HierarchicalData;

use Illuminate\Database\Eloquent\Relations\Relation;

interface Heirarchical
{
    public function ancestors(): Relation;
}