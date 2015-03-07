<?php
namespace Tests\Unit\Stubs\CountCache;

use Eloquence\Behaviours\CountCache\CountCache;
use Eloquence\Database\Model;

class Comment extends Model implements CountCache
{
    public function countCaches()
    {
        return [
            'num_comments' => 'Tests\Unit\Stubs\CountCache\Post',
            'Tests\Unit\Stubs\CountCache\User'
        ];
    }
}
