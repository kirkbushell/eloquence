<?php
namespace Tests\Acceptance\Models;

use Eloquence\Behaviours\CountCache\Countable;
use Eloquence\Behaviours\CountCache\HasCounts;
use Eloquence\Behaviours\CamelCased;
use Eloquence\Behaviours\Sluggable;
use Eloquence\Behaviours\SumCache\HasSums;
use Eloquence\Behaviours\SumCache\Summable;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Post extends Model implements Countable, Summable
{
    use CamelCased;
    use Sluggable;
    use HasCounts;
    use HasFactory;
    use HasSums;

    protected $fillable = [
        'user_id',
        'category_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function countedBy(): array
    {
        return ['user', 'category'];
    }

    public function slugStrategy()
    {
        return 'id';
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function summedBy(): array
    {
        return ['category' => ['total_comments' => 'comment_count']];
    }

    protected static function newFactory(): Factory
    {
        return PostFactory::new();
    }
}
