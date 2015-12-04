<?php
namespace Tests\Acceptance\Models;

use Eloquence\Behaviours\SumCache\SumCache;
use Eloquence\Behaviours\SumCache\Summable;
use Eloquence\Database\Traits\CamelCasing;
use Illuminate\Database\Eloquent\Model;

class Item extends Model implements SumCache
{
    use CamelCasing;
    use Summable;

    public function sumCaches()
    {
        return [
            'Tests\Acceptance\Models\Order',
            [
                'model' => 'Tests\Acceptance\Models\Order',
                'field' => 'itemTotalExplicit',
                'columnToSum' => 'total',
                'foreignKey' => 'orderId',
                'key' => 'id',
            ]
        ];
    }
}
