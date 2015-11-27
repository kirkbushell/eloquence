<?php
namespace Eloquence\Behaviours\SumCache;

interface SumCache
{
    /**
     * Should return an array of the sum caches that need to be updated when this
     * model's state changes. Use the following array below as an example when an Order
     * needs to update a User's order sum cache. These represent the default values used
     * by the behaviour.
     *
     *   return [ 'order_total' => [ 'User', 'total', 'user_id', 'id' ] ];
     *
     * So, to extend, the first argument should be an index representing the sum cache
     * field on the associated model. Next is a numerical array:
     *
     * 0 = The model to be used for the update
	 * 1 = The remote field that represents the value to sum *optional
     * 2 = The foreign_key for the relationship that RelatedSum will watch *optional
	 * 3 = The remote field that represents the key *optional
     *
     * If the latter 2 options are not provided, or if the sum cache option is a string representing
     * the model, then RelatedSum will assume the ID fields based on conventional standards.
     *
     * Ie. another way to setup a sum cache is like below. This is an identical configuration to above.
     *
     *   return [ 'order_total' => [ 'User', 'total' ] ];
     *
     * This can be simplified even further, like this:
     *
     *   return [ 'User' ];
     *
     * @return array
     */
	public function sumCaches();
}
