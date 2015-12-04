<?php
namespace Tests\Acceptance\Models;

use Eloquence\Behaviours\CamelCasing;
use Eloquence\Behaviours\Sluggable;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use \Eloquence\Behaviours\CamelCasing;
    use \Eloquence\Behaviours\Sluggable;

    public function posts()
    {
        return $this->hasMany('Tests\Acceptance\Models\Post');
    }

    public function slugStrategy()
    {
        return ['firstName', 'lastName'];
    }
}
