<?php
namespace Tests\Acceptance\Models;

use Eloquence\Behaviours\SumCache\HasSums;
use Eloquence\Behaviours\CamelCased;
use Eloquence\Behaviours\SumCache\Summable;
use Illuminate\Database\Eloquent\Model;

class Item extends Model implements Summable
{
    use CamelCased;
    use HasSums;

    public function summedBy(): array
    {
        return ['order' => 'total_items'];
    }
}
