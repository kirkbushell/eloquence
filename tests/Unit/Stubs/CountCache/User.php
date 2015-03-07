<?php
namespace Tests\Unit\Stubs\CountCache;

use Eloquence\Database\Traits\SluggableModel;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use SluggableModel;

    protected function slugStrategy()
    {
        return ['id'];
    }
}
