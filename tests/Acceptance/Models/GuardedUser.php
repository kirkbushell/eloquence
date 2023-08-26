<?php
namespace Tests\Acceptance\Models;

use Eloquence\Behaviours\CamelCased;
use Illuminate\Database\Eloquent\Model;

class GuardedUser extends Model
{
    use CamelCased;

    protected $table = 'users';

    /**
     * The attributes that are protected from mass assignment.
     *
     * @var array
     */
    protected $guarded = ['id'];
}
