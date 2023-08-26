<?php
namespace Tests\Acceptance\Models;

use Eloquence\Behaviours\CountCache\Countable;
use Eloquence\Behaviours\CountCache\HasCounts;
use Eloquence\Behaviours\CamelCased;
use Eloquence\Behaviours\Sluggable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Post extends Model implements Countable
{
    use CamelCased;
    use Sluggable;
    use HasCounts;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function countedBy(): array
    {
        return ['user'];
    }

    public function slugStrategy()
    {
        return 'id';
    }
}
