<?php
namespace Tests\Acceptance\Models;

use Eloquence\Database\Traits\CamelCaseModel;
use Eloquence\Database\Traits\SluggableModel;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use CamelCaseModel;
    use SluggableModel;

    public function posts()
    {
        return $this->hasMany('Tests\Acceptance\Models\Post');
    }

    public function slugStrategy()
    {
        return ['firstName', 'lastName'];
    }
}
