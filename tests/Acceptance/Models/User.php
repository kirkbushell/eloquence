<?php
namespace Tests\Acceptance\Models;

use Eloquence\Database\Traits\CamelCasing;
use Eloquence\Database\Traits\Sluggable;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use CamelCasing;
    use Sluggable;

    public function posts()
    {
        return $this->hasMany('Tests\Acceptance\Models\Post');
    }

    public function slugStrategy()
    {
        return ['firstName', 'lastName'];
    }
}
