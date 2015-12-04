<?php
namespace Tests\Unit\Stubs\CountCache;

use Eloquence\Database\Traits\Sluggable;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use Sluggable;

    protected function slugStrategy()
    {
        return ['id'];
    }
}
