<?php
namespace Tests\Acceptance\Models;

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
            [
                'model' => 'Tests\Acceptance\Models\User',
                'force' => true
            ]
        ];
    }
}
