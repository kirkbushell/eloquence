<?php
namespace Tests\Acceptance\Models;

use Eloquence\Behaviours\Countable;
use Eloquence\Behaviours\CamelCasing;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use \Eloquence\Behaviours\CamelCasing;
    use \Eloquence\Behaviours\Countable;

    public function countCaches()
    {
        return [
            'Tests\Acceptance\Models\Post',
            'Tests\Acceptance\Models\User',
        ];
    }
}
