<?php
namespace Tests\Acceptance\Models;

use Eloquence\Behaviours\HasCamelCasing;
use Illuminate\Database\Eloquent\Model;

class GuardedUser extends Model
{
    use HasCamelCasing;

    protected $table = 'users';

    /**
     * The attributes that are protected from mass assignment.
     *
     * @var array
     */
    protected $guarded = ['id'];
}
