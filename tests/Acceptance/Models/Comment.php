<?php
namespace Tests\Acceptance\Models;

use Eloquence\Behaviours\CountCache\Countable;
use Eloquence\Behaviours\CamelCasing;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comment extends Model
{
    use CamelCasing;
    use Countable;
    use SoftDeletes;

    public function countCaches()
    {
        return [
            'Tests\Acceptance\Models\Post',
            'Tests\Acceptance\Models\User',
        ];
    }
}
