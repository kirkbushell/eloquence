<?php
namespace Tests\Acceptance\Models;

use Eloquence\Behaviours\CountCache\CountedBy;
use Eloquence\Behaviours\CountCache\HasCounts;
use Eloquence\Behaviours\CamelCased;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comment extends Model
{
    use CamelCased;
    use HasCounts;
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'post_id',
    ];

    #[CountedBy]
    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    #[CountedBy]
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected static function newFactory(): Factory
    {
        return CommentFactory::new();
    }
}
