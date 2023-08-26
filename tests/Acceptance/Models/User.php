<?php
namespace Tests\Acceptance\Models;

use Eloquence\Behaviours\CamelCased;
use Eloquence\Behaviours\Sluggable;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use CamelCased;
    use Sluggable;

    public function slugStrategy()
    {
        return ['firstName', 'lastName'];
    }
}
