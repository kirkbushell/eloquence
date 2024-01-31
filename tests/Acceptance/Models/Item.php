<?php
namespace Tests\Acceptance\Models;

use Eloquence\Behaviours\SumCache\HasSums;
use Eloquence\Behaviours\HasCamelCasing;
use Eloquence\Behaviours\SumCache\SummedBy;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Item extends Model
{
    use HasCamelCasing;
    use HasSums;
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'amount',
        'order_id',
    ];

    #[SummedBy('amount', 'total_amount')]
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    protected static function newFactory(): Factory
    {
        return ItemFactory::new();
    }
}
