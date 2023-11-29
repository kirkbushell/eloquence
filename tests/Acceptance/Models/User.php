<?php
namespace Tests\Acceptance\Models;

use Eloquence\Behaviours\CamelCased;
use Eloquence\Behaviours\Sluggable;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Model
{
    use CamelCased;
    use HasFactory;
    use Sluggable;

    protected $fillable = [
        'post_count'
    ];

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    public function slugStrategy()
    {
        return ['firstName', 'lastName'];
    }

    protected static function newFactory(): Factory
    {
        return UserFactory::new();
    }
}
