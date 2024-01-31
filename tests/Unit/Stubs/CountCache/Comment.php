<?php
namespace Tests\Unit\Stubs\CountCache;

use Eloquence\Behaviours\CountCache\HasCounts;
use Eloquence\Database\Model;

class Comment extends Model
{
    use HasCounts;

    public function countCaches()
    {
        return [
            'num_comments' => 'Tests\Unit\Stubs\CountCache\Post',
            'Tests\Unit\Stubs\CountCache\User'
        ];
    }
}
