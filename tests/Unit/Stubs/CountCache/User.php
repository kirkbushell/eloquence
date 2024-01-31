<?php
namespace Tests\Unit\Stubs\CountCache;

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
