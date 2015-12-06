<?php
namespace tests\Acceptance\Models;

use Eloquence\Behaviours\CountCache\Countable;
use Eloquence\Behaviours\CamelCasing;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use CamelCasing;
    use Countable;

    public function countCaches()
    {
        return [
            'Tests\Acceptance\Models\Post',
            'Tests\Acceptance\Models\User',
        ];
    }
}
