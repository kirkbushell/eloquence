<?php
namespace Tests\Unit\Stubs;

use Eloquence\Behaviours\CountCache\CacheConfig;
use Eloquence\Behaviours\CountCache\Countable;
use Eloquence\Behaviours\CountCache\CountCache;
use Eloquence\Database\Model;

class RealModelStub extends Model
{
    use \Eloquence\Behaviours\CountCache\Countable;

    protected $dateFormat = \DateTime::ISO8601;

    protected $dates = ['dateField'];

    public $hidden = ['hiddenField', 'passwordHash'];

    public $fillable = ['myField', 'anotherField', 'some_field', 'hiddenField', 'passwordHash', 'dateField'];

    public function fakeRelationship()
    {
        return 'nothing';
    }

    /**
     * Should return an array of the count caches that need to be updated when this
     * model's state changes. Use the following array below as an example when a User
     * needs to update a Role's user count cache. These represent the default values used
     * by the behaviour.
     *
     *   return [ 'user_count' => [ 'Role', 'role_id', 'id' ] ];
     *
     * So, to extend, the first argument should be an index representing the counter cache
     * field on the associated model. Next is a numerical array:
     *
     * 0 = The model to be used for the update
     * 1 = The foreign_key for the relationship that RelatedCount will watch *optional
     * 2 = The remote field that represents the key *optional
     *
     * If the latter 2 options are not provided, or if the counter cache option is a string representing
     * the model, then RelatedCount will assume the ID fields based on conventional standards.
     *
     * Ie. another way to setup a counter cache is like below. This is an identical configuration to above.
     *
     *   return [ 'user_count' => 'Role' ];
     *
     * This can be simplified even further, like this:
     *
     *   return [ 'Role' ];
     *
     * @return array
     */
    public function countCaches()
    {
        return [
            'users_count' => ['Role', 'role_id', 'id'],
            'comments_count' => 'Post',
            'User'
        ];
    }
}
