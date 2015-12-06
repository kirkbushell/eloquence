<?php
namespace Tests\Acceptance\Models;

use Eloquence\Behaviours\CamelCasing;
use Eloquence\Behaviours\Sluggable;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use CamelCasing;
    use Sluggable;

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function slugStrategy()
    {
        return ['firstName', 'lastName'];
    }
}
