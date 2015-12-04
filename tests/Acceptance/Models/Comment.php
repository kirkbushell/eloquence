<?php
namespace Tests\Acceptance\Models;

use Eloquence\Behaviours\CountCache\Countable;
use Eloquence\Database\Traits\CamelCasing;
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
