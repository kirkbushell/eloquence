<?php
namespace Tests\Unit\Stubs\CountCache;

use Eloquence\Database\Traits\Slugged;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use Slugged;

    protected function slugStrategy()
    {
        return ['id'];
    }
}
