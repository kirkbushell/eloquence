<?php

namespace Eloquence\Behaviours\SumCache;

interface Summable
{
    /**
     * Returns a key->value array of the relationship you want to utilise to update the sum, followed
     * by the source field you wish to sum. For example, if you have an order model that has many items
     * and you wish to sum the item amount, you can return the following:
     *
     * ['order' => 'amount']
     *
     * Of course, if you want to customise the field saving the total as well, you can do that too:
     *
     * ['relationship' => ['aggregate_field' => 'source_field']]
     *
     * In real-world terms:
     *
     * ['order' => ['total_amount' => 'amount']]
     *
     * By default, the sum cache will take the source field, and add "_total" to it on the related model.
     *
     * @return array
     */
    public function summedBy(): array;
}
