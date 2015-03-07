<?php
namespace Tests\Acceptance\Models;

use Eloquence\Behaviours\CountCache\CountCache;
use Eloquence\Database\Traits\CamelCaseModel;
use Eloquence\Database\Traits\SluggableModel;
use Illuminate\Database\Eloquent\Model;

class Post extends Model implements CountCache
{
    use CamelCaseModel;
    use SluggableModel;

    public function countCaches()
    {
        return ['post_count' => ['Tests\Acceptance\Models\User', 'user_id', 'id']];
    }

    public function slugStrategy()
    {
        return 'id';
    }
}
