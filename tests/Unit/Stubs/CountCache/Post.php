<?php
namespace Tests\Unit\Stubs\CountCache;

use Eloquence\Behaviours\CountCache\Countable;
use Eloquence\Database\Model;

class Post extends Model
{
    use Countable;

    public function countCaches()
    {
        return [
            'posts_count' => ['Tests\Unit\Stubs\CountCache\User', 'user_id', 'id'],
            [
                'model' => 'Tests\Unit\Stubs\CountCache\User',
                'countField' => 'posts_count_explicit',
                'foreignKey' => 'user_id',
                'key' => 'id'
            ]
        ];
    }
}
