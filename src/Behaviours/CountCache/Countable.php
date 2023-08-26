<?php

namespace Eloquence\Behaviours\CountCache;

interface Countable
{
    /**
     * Returns a key->value array of the relationship you want to utilise to update the count, followed
     * by the field on that related model. For example, if you have a user model that has many posts
     * you can return the following:
     *
     * ['user']
     *
     * Of course you can customise the count field:
     *
     * ['user' => 'post_total']
     *
     * @return array
     */
    public function countedBy(): array;
}