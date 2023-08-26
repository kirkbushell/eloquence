<?php

namespace Eloquence\Behaviours\SumCache;

interface Summable
{
    /**
     * Returns a key->value array of the relationship you want to utilise to update the sum, followed
     * by the field on that related model. For example, if you have an order model that has many items
     * you can return the following:
     *
     * ['order']
     *
     * Of course you can customise the summed field:
     *
     * ['order' => 'total_items']
     *
     * @return array
     */
    public function summedBy(): array;
}