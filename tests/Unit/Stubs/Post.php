<?php
namespace Tests\Unit\Stubs;

use Eloquence\Behaviours\CountCache\HasCounts;
use Eloquence\Database\Model;

class Post extends Model
{
    use HasCounts;

    public function countCaches()
    {
        return [
            'posts_count' => ['Tests\Unit\Stubs\User', 'user_id', 'id'],
            [
                'model' => 'Tests\Unit\Stubs\User',
                'countField' => 'posts_count_explicit',
                'foreignKey' => 'user_id',
                'key' => 'id'
            ]
        ];
    }
}