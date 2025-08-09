<?php
namespace Tests\Unit\Stubs;

use Eloquence\Behaviours\CountCache\HasCounts;
use Eloquence\Behaviours\HierarchicalData\HasHierarchy;
use Eloquence\Behaviours\HierarchicalData\Heirarchical;
use Eloquence\Database\Model;

class Comment extends Model implements Heirarchical
{
    use HasCounts;
    use HasHierarchy;

    public function countCaches(): array
    {
        return [
            'num_comments' => 'Tests\Unit\Stubs\Post',
            'Tests\Unit\Stubs\User'
        ];
    }
}