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
        return ['postCount' => ['Tests\Acceptance\Models\User', 'userId', 'id']];
    }

    public function slugStrategy()
    {
        return 'id';
    }
}
