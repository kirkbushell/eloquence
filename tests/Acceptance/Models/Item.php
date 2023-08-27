<?php
namespace Tests\Acceptance\Models;

use Eloquence\Behaviours\SumCache\HasSums;
use Eloquence\Behaviours\CamelCased;
use Eloquence\Behaviours\SumCache\SummedBy;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use CamelCased;
    use HasSums;
    use HasFactory;

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
