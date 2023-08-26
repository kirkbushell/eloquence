<?php
namespace Tests\Acceptance\Models;

use Eloquence\Behaviours\CountCache\Countable;
use Eloquence\Behaviours\CountCache\HasCounts;
use Eloquence\Behaviours\CamelCased;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comment extends Model implements Countable
{
    use CamelCased;
    use HasCounts;
    use SoftDeletes;

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function countedBy(): array
    {
        return ['post', 'user'];
    }
}
