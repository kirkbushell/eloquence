<?php
namespace Tests\Unit\Stubs\CountCache;

use Eloquence\Behaviours\Sluggable;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use Sluggable;

    public function slugStrategy()
    {
        return ['id'];
    }
}
