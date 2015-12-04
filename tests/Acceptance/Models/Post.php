<?php
namespace Tests\Acceptance\Models;

use Eloquence\Behaviours\CountCache\CountCache;
use Eloquence\Database\Traits\CamelCasing;
use Eloquence\Database\Traits\Sluggable;
use Illuminate\Database\Eloquent\Model;

class Post extends Model implements CountCache
{
    use CamelCasing;
    use Sluggable;

    public function countCaches()
    {
        return [
            'postCount' => ['Tests\Acceptance\Models\User', 'userId', 'id'],
            [
                'model' => 'Tests\Acceptance\Models\User',
                'countField' => 'postCountExplicit',
                'foreignKey' => 'userId',
                'key' => 'id',
            ]
        ];
    }

    public function slugStrategy()
    {
        return 'id';
    }
}
