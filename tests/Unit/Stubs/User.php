<?php
namespace Tests\Unit\Stubs;

use Eloquence\Behaviours\HasSlugs;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use HasSlugs;

    public function slugStrategy()
    {
        return ['id'];
    }
}