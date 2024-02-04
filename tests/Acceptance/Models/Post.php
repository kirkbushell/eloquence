<?php
namespace Tests\Acceptance\Models;

use Eloquence\Behaviours\CountCache\CountedBy;
use Eloquence\Behaviours\CountCache\HasCounts;
use Eloquence\Behaviours\HasCamelCasing;
use Eloquence\Behaviours\HasSlugs;
use Eloquence\Behaviours\SumCache\HasSums;
use Eloquence\Behaviours\SumCache\SummedBy;
use Eloquence\Behaviours\ValueCache\HasValues;
use Eloquence\Behaviours\ValueCache\ValuedBy;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Post extends Model
{
    use HasCamelCasing;
    use HasSlugs;
    use HasCounts;
    use HasFactory;
    use HasSums;
    use HasValues;

    protected $fillable = [
        'user_id',
        'category_id',
        'publish_at',
    ];

    #[CountedBy(as: 'post_count')]
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function slugStrategy()
    {
        return 'id';
    }

    #[CountedBy]
    #[SummedBy(from: 'comment_count', as: 'total_comments')]
    #[ValuedBy(from: 'publish_at', as: 'last_activity_at')]
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    protected static function newFactory(): Factory
    {
        return PostFactory::new();
    }
}
