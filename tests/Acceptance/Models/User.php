<?php
namespace Tests\Acceptance\Models;

use Eloquence\Behaviours\HasCamelCasing;
use Eloquence\Behaviours\HasSlugs;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Model
{
    use HasCamelCasing;
    use HasFactory;
    use HasSlugs;

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
